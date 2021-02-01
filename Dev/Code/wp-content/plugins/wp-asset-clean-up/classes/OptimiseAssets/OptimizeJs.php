<?php
namespace WpAssetCleanUp\OptimiseAssets;

use WpAssetCleanUp\FileSystem;
use WpAssetCleanUp\CleanUp;
use WpAssetCleanUp\Main;
use WpAssetCleanUp\MetaBoxes;
use WpAssetCleanUp\Misc;
use WpAssetCleanUp\Preloads;

/**
 * Class OptimizeJs
 * @package WpAssetCleanUp
 */
class OptimizeJs
{
	/**
	 * @var float|int
	 */
	public static $cachedJsAssetsFileExpiresIn = 28800; // 8 hours in seconds (60 * 60 * 8)

	/**
	 *
	 */
	public function init()
	{
		add_action('wp_print_footer_scripts', array($this, 'prepareOptimizeList'), PHP_INT_MAX);
	}

	/**
	 *
	 */
	public function prepareOptimizeList()
	{
		// Are both Minify and Cache Dynamic JS disabled? No point in continuing and using extra resources as there is nothing to change
		if (! self::isWorthCheckingForOptimization()) {
			return;
		}

		global $wp_scripts;

		$jsOptimizeList = array();

		$wpScriptsList = array_unique(array_merge($wp_scripts->done, $wp_scripts->queue));

		// Collect all enqueued clean (no query strings) HREFs to later compare them against any hardcoded JS
		$allEnqueuedCleanScriptSrcs = array();

		foreach ($wpScriptsList as $scriptHandle) {
			if (isset(Main::instance()->wpAllScripts['registered'][$scriptHandle]->src) && ($src = Main::instance()->wpAllScripts['registered'][$scriptHandle]->src)) {
				$localAssetPath = OptimizeCommon::getLocalAssetPath($src, 'js');

				if (! $localAssetPath || ! file_exists($localAssetPath)) {
					continue; // not a local file
				}

				ob_start();
				$wp_scripts->do_item($scriptHandle);
				$scriptSourceTag = trim(ob_get_clean());

				$cleanScriptSrcFromTagArray = OptimizeCommon::getLocalCleanSourceFromTag($scriptSourceTag, 'src');
				$allEnqueuedCleanScriptSrcs[] = $cleanScriptSrcFromTagArray['source'];
			}
		}

		// [Start] Collect for caching
		foreach ($wpScriptsList as $handle) {
			if (! isset($wp_scripts->registered[$handle])) { continue; }

			$value = $wp_scripts->registered[$handle];

			$localAssetPath = OptimizeCommon::getLocalAssetPath($value->src, 'js');
			if (! $localAssetPath || ! file_exists($localAssetPath)) {
				continue; // not a local file
			}

			$optimizeValues = self::maybeOptimizeIt($value);

			if ( ! empty( $optimizeValues ) ) {
				$jsOptimizeList[] = $optimizeValues;
			}
		}

		wp_cache_add('wpacu_js_enqueued_srcs', $allEnqueuedCleanScriptSrcs);
		wp_cache_add('wpacu_js_optimize_list', $jsOptimizeList);
		// [End] Collect for caching
	}

	/**
	 * @param $value
	 *
	 * @return array
	 */
	public static function maybeOptimizeIt($value)
	{
		global $wp_version;

		$src = isset($value->src) ? $value->src : false;

		if (! $src) {
			return array();
		}

		$doFileMinify = true;

		if (! MinifyJs::isMinifyJsEnabled()) {
			$doFileMinify = false;
		} elseif (MinifyJs::skipMinify($src, $value->handle)) {
			$doFileMinify = false;
		}

		$fileVer = $dbVer = (isset($value->ver) && $value->ver) ? $value->ver : $wp_version;

		$handleDbStr = md5($value->handle);

		$transientName = 'wpacu_js_optimize_'.$handleDbStr;

		if (! isset($GLOBALS['from_location_inc'])) { $GLOBALS['from_location_inc'] = 1; }
			$fromLocation = ($GLOBALS['from_location_inc'] % 2) ? 'db' : 'local';
			$savedValues = OptimizeCommon::getTransient($transientName, $fromLocation);

			if ( $savedValues ) {
				$savedValuesArray = json_decode( $savedValues, ARRAY_A );

				if ( $savedValuesArray['ver'] !== $dbVer ) {
					// New File Version? Delete transient as it will be re-added to the database with the new version
					OptimizeCommon::deleteTransient($transientName);
				} else {
					$localPathToJsOptimized = str_replace( '//', '/', ABSPATH . $savedValuesArray['optimize_uri'] );

					// Do not load any minified JS file (from the database transient cache) if it doesn't exist
					// It will fallback to the original JS file
					if ( isset( $savedValuesArray['source_uri'] ) && file_exists( $localPathToJsOptimized ) ) {
						$GLOBALS['from_location_inc']++;

						return array(
							$savedValuesArray['source_uri'],
							$savedValuesArray['optimize_uri']
						);
					}
				}
			}

		if (strpos($src, '/wp-includes/') === 0) {
			$src = site_url() . $src;
		}

		$isJsFile = $jsContentBefore = false;

		if (Main::instance()->settings['cache_dynamic_loaded_js'] &&
			((strpos($src, '/?') !== false) || strpos($src, '.php?') !== false || Misc::endsWith($src, '.php')) &&
		    (strpos($src, site_url()) !== false)
		) {
			$pathToAssetDir = '';
			$sourceBeforeOptimization = $value->src;

			if (! ($jsContent = DynamicLoadedAssets::getAssetContentFrom('dynamic', $value))) {
				return array();
			}
		} else {
			$localAssetPath = OptimizeCommon::getLocalAssetPath($src, 'js');

			if (! file_exists($localAssetPath)) {
				return array();
			}

			$isJsFile = true;

			$pathToAssetDir = OptimizeCommon::getPathToAssetDir($value->src);
			$sourceBeforeOptimization = str_replace(ABSPATH, '/', $localAssetPath);

			$jsContent = $jsContentBefore = FileSystem::file_get_contents($localAssetPath);
		}

		if ($doFileMinify) {
			$jsContent = MinifyJs::applyMinification($jsContent);
		}

		if ($isJsFile && trim($jsContent, '; ') === trim($jsContentBefore, '; ')) {
			// The (static) JS file is already minified / No need to copy it in to the cache (save disk space)
			return array();
		}

		$jsContent = self::maybeDoJsFixes($jsContent, $pathToAssetDir . '/'); // Minify it and save it to /wp-content/cache/js/min/

		// Relative path to the new file
		// Save it to /wp-content/cache/js/{OptimizeCommon::$optimizedSingleFilesDir}/
		if ($fileVer !== $wp_version) {
			$fileVer = trim(str_replace(' ', '_', preg_replace('/\s+/', ' ', $fileVer)));
			$fileVer = (strlen($fileVer) > 50) ? substr(md5($fileVer), 0, 20) : $fileVer; // don't end up with too long filenames
		}

		$newFilePathUri  = self::getRelPathJsCacheDir() . OptimizeCommon::$optimizedSingleFilesDir . '/' . $value->handle . '-v' . $fileVer;

		if (isset($localAssetPath)) { // For static files only
			$sha1File = @sha1_file($localAssetPath);

			if ($sha1File) {
				$newFilePathUri .= '-' . $sha1File;
			}
		}

		$newFilePathUri .= '.js';

		$newLocalPath    = WP_CONTENT_DIR . $newFilePathUri; // Ful Local path
		$newLocalPathUrl = WP_CONTENT_URL . $newFilePathUri; // Full URL path

		if ($jsContent) {
			$jsContent = '/*! ' . $sourceBeforeOptimization . ' */' . "\n" . $jsContent;
		}

		$saveFile = FileSystem::file_put_contents($newLocalPath, $jsContent);

		if (! $saveFile || ! $jsContent) {
			// Fallback to the original JS if the optimized version can't be created or updated
			return array();
		}

		$saveValues = array(
			'source_uri'   => OptimizeCommon::getHrefRelPath($value->src),
			'optimize_uri' => OptimizeCommon::getHrefRelPath($newLocalPathUrl),
			'ver'          => $dbVer
		);

		// Add / Re-add (with new version) transient
		OptimizeCommon::setTransient($transientName, json_encode($saveValues));

		return array(
			OptimizeCommon::getHrefRelPath($value->src),
			OptimizeCommon::getHrefRelPath($newLocalPathUrl)
		);
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed
	 */
	public static function updateHtmlSourceOriginalToOptimizedJs($htmlSource)
	{
		$jsOptimizeList = wp_cache_get('wpacu_js_optimize_list') ?: array();
		$allEnqueuedCleanScriptSrcs = wp_cache_get('wpacu_js_enqueued_srcs') ?: array();

		preg_match_all('#(<script[^>]*src(|\s+)=(|\s+)[^>]*(>))|(<link[^>]*(as(\s+|)=(\s+|)(|"|\')script(|"|\'))[^>]*(>))#Umi', OptimizeCommon::cleanerHtmlSource($htmlSource), $matchesSourcesFromTags, PREG_SET_ORDER);

		foreach ($matchesSourcesFromTags as $matches) {
			$scriptSourceTag = $matches[0];

			if (strip_tags($scriptSourceTag) !== '') {
				// Hmm? Not a valid tag... Skip it...
				continue;
			}

			$forAttr = 'src';

			// Any preloads for the optimized script?
			// e.g. <link rel='preload' as='script' href='...' />
			if (strpos($scriptSourceTag, '<link') !== false) {
				$forAttr = 'href';
			}

			// Is it a local JS? Check if it's hardcoded (not enqueued the WordPress way)
			if ($cleanScriptSrcFromTagArray = OptimizeCommon::getLocalCleanSourceFromTag($scriptSourceTag, $forAttr)) {
				$cleanScriptSrcFromTag = $cleanScriptSrcFromTagArray['source'];
				$afterQuestionMark = $cleanScriptSrcFromTagArray['after_question_mark'];

				if (! in_array($cleanScriptSrcFromTag, $allEnqueuedCleanScriptSrcs)) {
					// Not in the final enqueued list? Most likely hardcoded (not added via wp_enqueue_scripts())
					// Emulate the object value (as the enqueued styles)
					$value = (object)array(
						'handle' => md5($cleanScriptSrcFromTag),
						'src'    => $cleanScriptSrcFromTag,
						'ver'    => md5($afterQuestionMark)
					);

					$optimizeValues = self::maybeOptimizeIt($value);

					if (! empty($optimizeValues)) {
						$jsOptimizeList[] = $optimizeValues;
					}
				}
			}

			if (empty($jsOptimizeList)) {
				continue;
			}

			foreach ($jsOptimizeList as $listValues) {
				// If the minified files are deleted (e.g. /wp-content/cache/ is cleared)
				// do not replace the JS file path to avoid breaking the website
				if (! file_exists(rtrim(ABSPATH, '/') . $listValues[1])) {
					continue;
				}

				$sourceUrl   = site_url() . $listValues[0];
				$optimizeUrl = site_url() . $listValues[1];

				if ($scriptSourceTag !== str_ireplace($sourceUrl, $optimizeUrl, $scriptSourceTag)) {
					$newLinkSourceTag = self::updateOriginalToOptimizedTag($scriptSourceTag, $sourceUrl, $optimizeUrl);
					$htmlSource = str_replace($scriptSourceTag, $newLinkSourceTag, $htmlSource);
					break;
				}
			}
		}

		return $htmlSource;
	}

	/**
	 * @param $scriptSourceTag
	 * @param $sourceUrl
	 * @param $optimizeUrl
	 *
	 * @return mixed
	 */
	public static function updateOriginalToOptimizedTag($scriptSourceTag, $sourceUrl, $optimizeUrl)
	{
		$newScriptSourceTag = str_replace($sourceUrl, $optimizeUrl, $scriptSourceTag);

		// Needed in case it's added to the Combine JS exceptions list
		if (CombineJs::proceedWithJsCombine()) {
			$newScriptSourceTag = str_ireplace('<script ', '<script data-wpacu-script-src-before="'.$sourceUrl.'" ', $newScriptSourceTag);
		}

		// Strip ?ver=
		$toStrip = Misc::extractBetween($newScriptSourceTag, '?ver=', '>');

		if (in_array(substr($toStrip, -1), array('"', "'"))) {
			$toStrip = '?ver='. trim(trim($toStrip, '"'), "'");
			$newScriptSourceTag = str_replace($toStrip, '', $newScriptSourceTag);
		}

		global $wp_version;

		$newScriptSourceTag = str_replace('.js&#038;ver='.$wp_version, '.js', $newScriptSourceTag);
		$newScriptSourceTag = str_replace('.js&#038;ver=', '.js?ver=', $newScriptSourceTag);

		return $newScriptSourceTag;
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed|void
	 */
	public static function alterHtmlSource($htmlSource)
	{
		// There has to be at least one "<script", otherwise, it could be a feed request or something similar (not page, post, homepage etc.)
		if (stripos($htmlSource, '<script') === false) {
			return $htmlSource;
		}

		/* [wpacu_pro] */$htmlSource = apply_filters('wpacu_pro_maybe_move_jquery_after_body_tag', $htmlSource);/* [/wpacu_pro] */

		// Are there any assets unloaded where their "children" are ignored?
		// Since they weren't dequeued the WP way (to avoid unloading the "children"), they will be stripped here
		if (! Main::instance()->preventAssetsSettings()) {
			$ignoreChild = Main::instance()->getIgnoreChildren();

			if (isset($ignoreChild['scripts']) && ! empty($ignoreChild['scripts'])) {
				foreach ($ignoreChild['scripts'] as $scriptSrc) {
					$htmlSource = CleanUp::cleanScriptTagFromHtmlSource($scriptSrc, $htmlSource);
				}
			}
		}

		/*
		 * The JavaScript files only get cached if they are minified or are loaded like /?custom-js=version - /script.php?ver=1 etc.
		 * #optimizing
		 * STEP 2: Load optimize-able caching list and replace the original source URLs with the new cached ones
		 */

		// At least minify or cache dynamic loaded JS has to be enabled to proceed
		if (self::isWorthCheckingForOptimization()) {
			// 'wpacu_js_optimize_list' caching list is also checked; if it's empty, no optimization is made
			$htmlSource = self::updateHtmlSourceOriginalToOptimizedJs($htmlSource);
		}

		if (! Main::instance()->preventAssetsSettings()) {
			$preloads = Preloads::instance()->getPreloads();

			if (isset($preloads['scripts']) && ! empty($preloads['scripts'])) {
				$htmlSource = Preloads::appendPreloadsForScriptsToHead($htmlSource);
			}

			$htmlSource = str_replace(Preloads::DEL_SCRIPTS_PRELOADS, '', $htmlSource);
		}

		$proceedWithCombineOnThisPage = true;

		// If "Do not combine JS on this page" is checked in "Asset CleanUp Options" side meta box
		// Works for posts, pages and custom post types
		if (defined('WPACU_CURRENT_PAGE_ID') && WPACU_CURRENT_PAGE_ID > 0) {
			$pageOptions = MetaBoxes::getPageOptions( WPACU_CURRENT_PAGE_ID );

			// 'no_js_optimize' refers to avoid the combination of JS files
			if ( isset( $pageOptions['no_js_optimize'] ) && $pageOptions['no_js_optimize'] ) {
				$proceedWithCombineOnThisPage = false;
			}
		}

		if ($proceedWithCombineOnThisPage) {
			$htmlSource = CombineJs::doCombine($htmlSource);
		}

		if (! Main::instance()->preventAssetsSettings() && Main::instance()->settings['minify_loaded_js'] && Main::instance()->settings['minify_loaded_js_inline']) {
			$htmlSource = MinifyJs::minifyInlineScriptTags($htmlSource);
		}

		// Final cleanups
		$htmlSource = preg_replace('#<script(\s+|)data-wpacu-script-src-before=(["\'])' . '(.*)' . '(\1)#Usmi', '<script ', $htmlSource);
		$htmlSource = preg_replace('#<script(\s+|)data-wpacu-script-handle=(["\'])'     . '(.*)' . '(\1)#Usmi', '<script ', $htmlSource);
		$htmlSource = preg_replace('#<script data-wpacu-to-be-preloaded-basic=\'1\' data-wpacu-script-handle=(["\'])' . '(.*)' . '(\1)#Usmi', '<script data-wpacu-to-be-preloaded-basic=\'1\' ', $htmlSource);

		return $htmlSource;
	}

	/**
	 * @return string
	 */
	public static function getRelPathJsCacheDir()
	{
		return OptimizeCommon::getRelPathPluginCacheDir().'js/'; // keep trailing slash at the end
	}

	/**
	 * @param $scriptSrcs
	 * @param $htmlSource
	 *
	 * @return array
	 */
	public static function getScriptTagsFromSrcs($scriptSrcs, $htmlSource)
	{
		$scriptTags = array();

		$cleanerHtmlSource = OptimizeCommon::cleanerHtmlSource($htmlSource);

		foreach ($scriptSrcs as $scriptSrc) {
			$scriptSrc = str_replace('{site_url}', '', $scriptSrc);

			preg_match_all('#<script[^>]*src(|\s+)=(|\s+)[^>]*'. preg_quote($scriptSrc, '/'). '.*(>)(.*|)</script>#Usmi', $cleanerHtmlSource, $matchesFromSrc, PREG_SET_ORDER);

			if (isset($matchesFromSrc[0][0]) && strip_tags($matchesFromSrc[0][0]) === '') {
				$scriptTags[] = trim($matchesFromSrc[0][0]);
			}
		}

		return $scriptTags;
	}

	/**
	 * @param $strFind
	 * @param $strReplaceWith
	 * @param $string
	 *
	 * @return mixed
	 */
	public static function strReplaceOnce($strFind, $strReplaceWith, $string)
	{
		if ( strpos($string, $strFind) === false ) {
			return $string;
		}

		$occurrence = strpos($string, $strFind);
		return substr_replace($string, $strReplaceWith, $occurrence, strlen($strFind));
	}

	/**
	 * @param $jsContent
	 * @param $appendBefore
	 *
	 * @return mixed
	 */
	public static function maybeDoJsFixes($jsContent, $appendBefore)
	{
		// Relative URIs for CSS Paths
		// For code such as:
		// $(this).css("background", "url('../images/image-1.jpg')");

		$jsContentPathReps = array(
			'url("../' => 'url("'.$appendBefore.'../',
			"url('../" => "url('".$appendBefore.'../',
			'url(../'  => 'url('.$appendBefore.'../',

			'url("./'  => 'url("'.$appendBefore.'./',
			"url('./"  => "url('".$appendBefore.'./',
			'url(./'   => 'url('.$appendBefore.'./'
		);

		$jsContent = str_replace(array_keys($jsContentPathReps), array_values($jsContentPathReps), $jsContent);

		$jsContent = trim($jsContent);

		if (substr($jsContent, -1) !== ';') {
			$jsContent .= "\n" . ';'; // add semicolon as the last character
		}

		return $jsContent;
	}

	/**
	 * @param string $returnType
	 * 'list' - will return the list of plugins that have JS optimization enabled
	 * 'if_enabled' - will stop when it finds the first one (any order) and return true
	 * @return array|bool
	 */
	public static function isOptimizeJsEnabledByOtherParty($returnType = 'list')
	{
		$pluginsToCheck = array(
			'autoptimize/autoptimize.php'            => 'Autoptimize',
			'wp-rocket/wp-rocket.php'                => 'WP Rocket',
			'wp-fastest-cache/wpFastestCache.php'    => 'WP Fastest Cache',
			'w3-total-cache/w3-total-cache.php'      => 'W3 Total Cache',
			'sg-cachepress/sg-cachepress.php'        => 'SG Optimizer',
			'fast-velocity-minify/fvm.php'           => 'Fast Velocity Minify',
			'litespeed-cache/litespeed-cache.php'    => 'LiteSpeed Cache',
			'swift-performance-lite/performance.php' => 'Swift Performance Lite',
			'breeze/breeze.php'                      => 'Breeze – WordPress Cache Plugin'
		);

		$jsOptimizeEnabledIn = array();

		foreach ($pluginsToCheck as $plugin => $pluginTitle) {
			// "Autoptimize" check
			if ($plugin === 'autoptimize/autoptimize.php' && Misc::isPluginActive($plugin) && get_option('autoptimize_js')) {
				$jsOptimizeEnabledIn[] = $pluginTitle;

				if ($returnType === 'if_enabled') { return true; }
			}

			// "WP Rocket" check
			if ($plugin === 'wp-rocket/wp-rocket.php' && Misc::isPluginActive($plugin)) {
				if (function_exists('get_rocket_option')) {
					$wpRocketMinifyJs = get_rocket_option('minify_js');
					$wpRocketMinifyConcatenateJs = get_rocket_option('minify_concatenate_js');
				} else {
					$wpRocketSettings  = get_option('wp_rocket_settings');
					$wpRocketMinifyJs = isset($wpRocketSettings['minify_js']) ? $wpRocketSettings['minify_js'] : false;
					$wpRocketMinifyConcatenateJs = isset($wpRocketSettings['minify_concatenate_js']) ? $wpRocketSettings['minify_concatenate_js'] : false;
				}

				if ($wpRocketMinifyJs || $wpRocketMinifyConcatenateJs) {
					$jsOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "WP Fastest Cache" check
			if ($plugin === 'wp-fastest-cache/wpFastestCache.php' && Misc::isPluginActive($plugin)) {
				$wpfcOptionsJson = get_option('WpFastestCache');
				$wpfcOptions = @json_decode($wpfcOptionsJson, ARRAY_A);

				if (isset($wpfcOptions['wpFastestCacheMinifyJs']) || isset($wpfcOptions['wpFastestCacheCombineJs'])) {
					$jsOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "W3 Total Cache" check
			if ($plugin === 'w3-total-cache/w3-total-cache.php' && Misc::isPluginActive($plugin)) {
				$w3tcConfigMaster = Misc::getW3tcMasterConfig();
				$w3tcEnableJs = (int)trim(Misc::extractBetween($w3tcConfigMaster, '"minify.js.enable":', ','), '" ');

				if ($w3tcEnableJs === 1) {
					$jsOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "SG Optimizer" check
			if ($plugin === 'sg-cachepress/sg-cachepress.php' && Misc::isPluginActive($plugin)) {
				if (class_exists('\SiteGround_Optimizer\Options\Options') && method_exists('\SiteGround_Optimizer\Options\Options', 'is_enabled')) {
					if (@\SiteGround_Optimizer\Options\Options::is_enabled( 'siteground_optimizer_optimize_javascript')) {
						$jsOptimizeEnabledIn[] = $pluginTitle;

						if ($returnType === 'if_enabled') { return true; }
					}
				}
			}

			// "Fast Velocity Minify" check
			if ($plugin === 'fast-velocity-minify/fvm.php' && Misc::isPluginActive($plugin)) {
				// It's enough if it's active due to its configuration
				$jsOptimizeEnabledIn[] = $pluginTitle;

				if ($returnType === 'if_enabled') { return true; }
			}

			// "LiteSpeed Cache" check
			if ($plugin === 'litespeed-cache/litespeed-cache.php' && Misc::isPluginActive($plugin) && ($liteSpeedCacheConf = apply_filters('litespeed_cache_get_options', get_option('litespeed-cache-conf')))) {
				if ( (isset($liteSpeedCacheConf['js_minify']) && $liteSpeedCacheConf['js_minify'])
				     || (isset($liteSpeedCacheConf['js_combine']) && $liteSpeedCacheConf['js_combine']) ) {
					$jsOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "Swift Performance Lite" check
			if ($plugin === 'swift-performance-lite/performance.php' && Misc::isPluginActive($plugin)
			    && class_exists('Swift_Performance_Lite') && method_exists('Swift_Performance_Lite', 'check_option')) {
				if ( @\Swift_Performance_Lite::check_option('merge-scripts', 1) ) {
					$jsOptimizeEnabledIn[] = $pluginTitle;
				}

				if ($returnType === 'if_enabled') { return true; }
			}

			// "Breeze – WordPress Cache Plugin"
			if ($plugin === 'breeze/breeze.php' && Misc::isPluginActive($plugin)) {
				$breezeBasicSettings    = get_option('breeze_basic_settings');
				$breezeAdvancedSettings = get_option('breeze_advanced_settings');

				if (isset($breezeBasicSettings['breeze-minify-js'], $breezeAdvancedSettings['breeze-group-js'])
				    && $breezeBasicSettings['breeze-minify-js'] && $breezeAdvancedSettings['breeze-group-js']) {
					$jsOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}
		}

		if ($returnType === 'if_enabled') { return false; }

		return $jsOptimizeEnabledIn;
	}

	/**
	 * @return bool
	 */
	public static function isWorthCheckingForOptimization()
	{
		// At least one of these options have to be enabled
		// Otherwise, we will not perform specific useless actions and save resources
		return MinifyJs::isMinifyJsEnabled() ||
		       Main::instance()->settings['cache_dynamic_loaded_js'];
	}

	}
