<?php
namespace WpAssetCleanUp\OptimiseAssets;

use WpAssetCleanUp\Preloads;
use WpAssetCleanUp\FileSystem;
use WpAssetCleanUp\CleanUp;
use WpAssetCleanUp\Main;
use WpAssetCleanUp\MetaBoxes;
use WpAssetCleanUp\Misc;

/**
 * Class OptimizeCss
 * @package WpAssetCleanUp
 */
class OptimizeCss
{
	/**
	 * @var float|int
	 */
	public static $cachedCssAssetsFileExpiresIn = 28800; // 8 hours in seconds (60 * 60 * 8)

	/**
	 *
	 */
	public function init()
	{
		add_action('init', array($this, 'triggersAfterInit'));
		add_action('wp_footer', array($this, 'prepareOptimizeList'), PHP_INT_MAX);
	}

	/**
	 *
	 */
	public function triggersAfterInit()
	{
		if (self::isInlineCssEnabled()) {
			$allPatterns = self::getAllInlineChosenPatterns();

			if (! empty($allPatterns)) {
				// Make "Inline CSS Files" compatible with "Optimize CSS Delivery" from WP Rocket
				add_filter('rocket_async_css_regex_pattern', static function($regex) {
					return '/(?=<link(?!.*wpacu-to-be-inlined.*)[^>]*\s(rel\s*=\s*[\'"]stylesheet["\']))<link(?!.*wpacu-to-be-inlined.*)[^>]*\shref\s*=\s*[\'"]([^\'"]+)[\'"](.*)>/iU';
				});

				add_filter('style_loader_tag', static function($styleTag) use ($allPatterns) {
					preg_match_all('#<link[^>]*stylesheet[^>]*(' . implode('|', $allPatterns) . ').*(>)#Usmi',
						$styleTag, $matchesSourcesFromTags, PREG_SET_ORDER);

					if (! empty($matchesSourcesFromTags)) {
						return str_replace('<link ', '<link wpacu-to-be-inlined=\'1\' ', $styleTag);
					}

					return $styleTag;
				}, 10, 1);
			}
		}
	}

	/**
	 * @return array
	 */
	public static function getAllInlineChosenPatterns()
	{
		$inlineCssFilesPatterns = trim(Main::instance()->settings['inline_css_files_list']);

		$allPatterns = array();

		if (strpos($inlineCssFilesPatterns, "\n")) {
			// Multiple values (one per line)
			foreach (explode("\n", $inlineCssFilesPatterns) as $inlinePattern) {
				$allPatterns[] = preg_quote(trim($inlinePattern), '/');
			}
		} else {
			// Only one value?
			$allPatterns[] = preg_quote(trim($inlineCssFilesPatterns), '/');
		}

		// Strip any empty values
		$allPatterns = array_filter($allPatterns);

		return $allPatterns;
	}

	/**
	 *
	 */
	public function prepareOptimizeList()
	{
		if (! self::isWorthCheckingForOptimization()) {
			return;
		}

		global $wp_styles;

		$allStylesHandles = wp_cache_get('wpacu_all_styles_handles');
		if (empty($allStylesHandles)) {
			return;
		}

		// [Start] Collect for caching
		$wpStylesDone = $wp_styles->done;
		$wpStylesRegistered = $wp_styles->registered;

		// Collect all enqueued clean (no query strings) HREFs to later compare them against any hardcoded CSS
		$allEnqueuedCleanLinkHrefs = array();

		foreach ($wpStylesDone as $styleHandle) {
			if (isset(Main::instance()->wpAllStyles['registered'][$styleHandle]->src) && ($src = Main::instance()->wpAllStyles['registered'][$styleHandle]->src)) {
				$localAssetPath = OptimizeCommon::getLocalAssetPath($src, 'css');

				if (! $localAssetPath || ! file_exists($localAssetPath)) {
					continue; // not a local file
				}

				ob_start();
				$wp_styles->do_item($styleHandle);
				$linkSourceTag = trim(ob_get_clean());

				$cleanLinkHrefFromTagArray = OptimizeCommon::getLocalCleanSourceFromTag($linkSourceTag, 'href');
				$allEnqueuedCleanLinkHrefs[] = $cleanLinkHrefFromTagArray['source'];
			}
		}

		$cssOptimizeList = array();

		foreach ($wpStylesDone as $handle) {
			if (! isset($wpStylesRegistered[$handle])) {
				continue;
			}

			$value = $wpStylesRegistered[$handle];

			$localAssetPath = OptimizeCommon::getLocalAssetPath($value->src, 'css');
			if (! $localAssetPath || ! file_exists($localAssetPath)) {
				continue; // not a local file
			}

			$optimizeValues = self::maybeOptimizeIt($value);

			if (! empty($optimizeValues)) {
				$cssOptimizeList[] = $optimizeValues;
			}
		}

		if (empty($cssOptimizeList)) {
			return;
		}

		wp_cache_add('wpacu_css_enqueued_hrefs', $allEnqueuedCleanLinkHrefs);
		wp_cache_add('wpacu_css_optimize_list', $cssOptimizeList);
		// [End] Collect for caching
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function maybeOptimizeIt($value)
	{
		global $wp_version;

		$src = isset($value->src) ? $value->src : false;

		if (! $src) {
			return array();
		}

		$doFileMinify = true;

		if (! MinifyCss::isMinifyCssEnabled()) {
			$doFileMinify = false;
		} elseif (MinifyCss::skipMinify($src, $value->handle)) {
			$doFileMinify = false;
		}

		$fileVer = $dbVer = (isset($value->ver) && $value->ver) ? $value->ver : $wp_version;

		$handleDbStr = md5($value->handle);

		$transientName = 'wpacu_css_optimize_'.$handleDbStr;

		if (! isset($GLOBALS['from_location_inc'])) { $GLOBALS['from_location_inc'] = 1; }
		    $fromLocation = ($GLOBALS['from_location_inc'] % 2) ? 'db' : 'local';
			$savedValues = OptimizeCommon::getTransient($transientName, $fromLocation);

			if ( $savedValues ) {
				$savedValuesArray = json_decode( $savedValues, ARRAY_A );

				if ( $savedValuesArray['ver'] !== $dbVer ) {
					// New File Version? Delete transient as it will be re-added to the database with the new version
					OptimizeCommon::deleteTransient($transientName);
				} else {
					$localPathToCssOptimized = str_replace( '//', '/', ABSPATH . $savedValuesArray['optimize_uri'] );

					if ( isset( $savedValuesArray['source_uri'] ) && file_exists( $localPathToCssOptimized ) ) {
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

		$isCssFile = false;

		if (Main::instance()->settings['cache_dynamic_loaded_css'] &&
		    $value->handle === 'sccss_style' &&
		    in_array('simple-custom-css/simple-custom-css.php', apply_filters('active_plugins', get_option('active_plugins')))
		) {
			$pathToAssetDir = '';
			$sourceBeforeOptimization = $value->src;

			if (! ($cssContent = DynamicLoadedAssets::getAssetContentFrom('simple-custom-css', $value))) {
				return array();
			}
		} elseif (Main::instance()->settings['cache_dynamic_loaded_css'] &&
            ((strpos($src, '/?') !== false) || strpos($src, '.php?') !== false || Misc::endsWith($src, '.php')) &&
			(strpos($src, site_url()) !== false)
		) {
			$pathToAssetDir = '';
			$sourceBeforeOptimization = $value->src;

			if (! ($cssContent = DynamicLoadedAssets::getAssetContentFrom('dynamic', $value))) {
				return array();
			}
		} else {
			/*
			 * All the CSS that exists as a .css file within the plugins/theme
			 */
			$localAssetPath = OptimizeCommon::getLocalAssetPath($src, 'css');

			if (! file_exists($localAssetPath)) {
				return array();
			}

			$isCssFile = true;

			$pathToAssetDir = OptimizeCommon::getPathToAssetDir($src);

			$cssContent = FileSystem::file_get_contents($localAssetPath, 'combine_css_imports');

			$sourceBeforeOptimization = str_replace(ABSPATH, '/', $localAssetPath);
		}

		/*
		 * [START] CSS Content Optimization
		*/
			// If there are no changes from this point, do not optimize (keep the file where it is)
			$cssContentBefore = $cssContent;

			if (Main::instance()->settings['google_fonts_display']) {
				// Any "font-display" enabled in "Settings" - "Google Fonts"?
				$cssContent = FontsGoogle::alterGoogleFontUrlFromCssContent($cssContent);
			}

			// Move any @imports to top; This also strips any @imports to Google Fonts if the option is chosen
			$cssContent = self::importsUpdate($cssContent);

			if ($doFileMinify) {
				// Minify this file?
				$cssContent = MinifyCss::applyMinification($cssContent) ?: $cssContent;
			}

			if (Main::instance()->settings['google_fonts_remove']) {
				$cssContent = FontsGoogleRemove::cleanFontFaceReferences($cssContent);
			}

			// No changes were made, thus, there's no point in changing the original file location
			if ($isCssFile && trim($cssContentBefore) === trim($cssContent)) {
				// There's no point in changing the original CSS (static) file location
				return false;
			}

			$cssContent = self::maybeFixCssContent($cssContent, $pathToAssetDir . '/'); // Path
		/*
         * [END] CSS Content Optimization
		*/

		// Relative path to the new file
		// Save it to /wp-content/cache/css/{OptimizeCommon::$optimizedSingleFilesDir}/
		if ($fileVer !== $wp_version) {
			$fileVer = trim(str_replace(' ', '_', preg_replace('/\s+/', ' ', $fileVer)));
			$fileVer = (strlen($fileVer) > 50) ? substr(md5($fileVer), 0, 20) : $fileVer; // don't end up with too long filenames
		}

		$newFilePathUri  = self::getRelPathCssCacheDir() . OptimizeCommon::$optimizedSingleFilesDir . '/' . $value->handle . '-v' . $fileVer;

		if (isset($localAssetPath)) { // could be from "/?custom-css=" so a check is needed
			$sha1File = @sha1_file($localAssetPath);

			if ($sha1File) {
				$newFilePathUri .= '-' . $sha1File;
			}
		}

		$newFilePathUri .= '.css';

		$newLocalPath    = WP_CONTENT_DIR . $newFilePathUri; // Ful Local path
		$newLocalPathUrl = WP_CONTENT_URL . $newFilePathUri; // Full URL path

		if ($cssContent) {
			$cssContent = '/*! ' . $sourceBeforeOptimization . ' */' . $cssContent;
		}

		$saveFile = FileSystem::file_put_contents($newLocalPath, $cssContent);

		if (! $saveFile && ! $cssContent) {
			// Fallback to the original CSS if the optimized version can't be created or updated
			return array();
		}

		$saveValues = array(
			'source_uri'   => OptimizeCommon::getHrefRelPath($src),
			'optimize_uri' => OptimizeCommon::getHrefRelPath($newLocalPathUrl),
			'ver'          => $dbVer
		);

		// Add / Re-add (with new version) transient
		OptimizeCommon::setTransient($transientName, json_encode($saveValues));

		return array(
			OptimizeCommon::getHrefRelPath($src),
			OptimizeCommon::getHrefRelPath($newLocalPathUrl)
		);
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed|void
	 */
	public static function alterHtmlSource($htmlSource)
	{
		// There has to be at least one "<link" or "<style", otherwise, it could be a feed request or something similar (not page, post, homepage etc.)
		if (stripos($htmlSource, '<link') === false && stripos($htmlSource, '<style') === false) {
			return $htmlSource;
		}

		// Are there any assets unloaded where their "children" are ignored?
		// Since they weren't dequeued the WP way (to avoid unloading the "children"), they will be stripped here
		if (! Main::instance()->preventAssetsSettings()) {
			$ignoreChild = Main::instance()->getIgnoreChildren();

			if (isset($ignoreChild['styles']) && ! empty($ignoreChild['styles'])) {
				foreach ($ignoreChild['styles'] as $styleSrc) {
					$htmlSource = CleanUp::cleanLinkTagFromHtmlSource($styleSrc, $htmlSource);
				}
			}
		}

		if (self::isInlineCssEnabled()) {
			$htmlSource = self::doInline($htmlSource);
		}

		if (self::isWorthCheckingForOptimization()) {
			// 'wpacu_css_optimize_list' caching list is also checked; if it's empty, no optimization is made
			$htmlSource = self::updateHtmlSourceOriginalToOptimizedCss($htmlSource);
		}

		if (! Main::instance()->preventAssetsSettings()) {
			$htmlSource = Preloads::instance()->doChanges($htmlSource);
		}

		$proceedWithCombineOnThisPage = true;

		// If "Do not combine CSS on this page" is checked in "Asset CleanUp: Options" side meta box
		// Works for posts, pages and custom post types
		if (defined('WPACU_CURRENT_PAGE_ID') && WPACU_CURRENT_PAGE_ID > 0) {
			$pageOptions = MetaBoxes::getPageOptions(WPACU_CURRENT_PAGE_ID);

			// 'no_css_optimize' refers to avoid the combination of CSS files
			if ( isset( $pageOptions['no_css_optimize'] ) && $pageOptions['no_css_optimize'] ) {
				$proceedWithCombineOnThisPage = false;
			}
		}

		if ($proceedWithCombineOnThisPage) {
			$htmlSource = CombineCss::doCombine($htmlSource);
		}

		if (! Main::instance()->preventAssetsSettings() && Main::instance()->settings['minify_loaded_css'] && Main::instance()->settings['minify_loaded_css_inline']) {
			$htmlSource = MinifyCss::minifyInlineStyleTags($htmlSource);
		}

		// Final cleanups
		$htmlSource = preg_replace('#<link(\s+|)data-wpacu-link-href-before=(["\'])' . '(.*)' . '(\1)#Usmi', '<link ', $htmlSource);
		$htmlSource = preg_replace('#<link(\s+|)data-wpacu-style-handle=(["\'])'     . '(.*)' . '(\1)#Usmi', '<link ', $htmlSource);
		$htmlSource = preg_replace('#<link data-wpacu-to-be-preloaded-basic=\'1\' data-wpacu-style-handle=(["\'])'     . '(.*)' . '(\1)#Usmi', '<link data-wpacu-to-be-preloaded-basic=\'1\' ', $htmlSource);

		// Alter HTML Source for Google Fonts Optimization / Removal
		$htmlSource = FontsGoogle::alterHtmlSource($htmlSource);

		return $htmlSource;
	}

	/**
	 * @return string
	 */
	public static function getRelPathCssCacheDir()
	{
		return OptimizeCommon::getRelPathPluginCacheDir().'css/'; // keep trailing slash at the end
	}

	/**
	 * @param $firstLinkHref
	 * @param $htmlSource
	 *
	 * @return string
	 */
	public static function getFirstLinkTag($firstLinkHref, $htmlSource)
	{
		preg_match_all('#<link[^>]*stylesheet[^>]*(>)#Usmi', $htmlSource, $matches);
		foreach ($matches[0] as $matchTag) {
			if (strpos($matchTag, $firstLinkHref) !== false) {
				return trim($matchTag);
			}
		}

		return '';
	}

	/**
	 *
	 * @param $cssContent
	 * @param $appendBefore
	 * @param $fix
	 *
	 * @return mixed
	 */
	public static function maybeFixCssContent($cssContent, $appendBefore, $fix = 'path')
	{
		// Updates (background | font etc.) URLs to the right path and others
		if ($fix === 'path') {
			// Clear any extra spaces between @import and the single/double quotes
			$cssContent = preg_replace('/@import(\s+|)([\'"])/i', '@import \\2', $cssContent);

			$cssContentPathReps = array(
				// @import with url(), background-image etc.
				'url("../' => 'url("'.$appendBefore.'../',
				"url('../" => "url('".$appendBefore.'../',
				'url(../'  => 'url('.$appendBefore.'../',

				'url("./'  => 'url("'.$appendBefore.'./',
				"url('./"  => "url('".$appendBefore.'./',
				'url(./'   => 'url('.$appendBefore.'./',

				// @import without URL
				'@import "../' => '@import "'.$appendBefore.'../',
				"@import '../" => "@import '".$appendBefore.'../',

				'@import "./'  => '@import "'.$appendBefore.'./',
				"@import './"  => "@import '".$appendBefore.'./'
			);

			$cssContent = str_replace(array_keys($cssContentPathReps), array_values($cssContentPathReps), $cssContent);

			// Rare cases
			$cssContent = preg_replace('/url\((\s+)http/i', 'url(http', $cssContent);

			// Avoid Background URLs starting with "data", "http" or "https" as they do not need to have a path updated
			preg_match_all('/url\((?![\'"]?(?:data|http|https):)[\'"]?([^\'")]*)[\'"]?\)/i', $cssContent, $matches);

			// If it start with forward slash (/), it doesn't need fix, just skip it
			// Also skip ../ types as they were already processed
			$toSkipList = array("url('/", 'url("/', 'url(/');

			foreach ($matches[0] as $match) {
				$fullUrlMatch = trim($match);

				foreach ($toSkipList as $toSkip) {
					if (substr($fullUrlMatch, 0, strlen($toSkip)) === $toSkip) {
						continue 2; // doesn't need any fix, go to the next match
					}
				}

				// Go through all situations: with and without quotes, with traversal directory (e.g. ../../)
				$alteredMatch = str_replace(
					array('url("', "url('"),
					array('url("' . $appendBefore, "url('" . $appendBefore),
					$fullUrlMatch
				);

				$alteredMatch = trim($alteredMatch);

				if (! in_array($fullUrlMatch{4}, array("'", '"', '/', '.'))) {
					$alteredMatch = str_replace('url(', 'url(' . $appendBefore, $alteredMatch);
					$alteredMatch = str_replace(array('")', '\')'), ')', $alteredMatch);
				}

				// Finally, apply the changes
				$cssContent = str_replace($fullUrlMatch, $alteredMatch, $cssContent);

				// Bug fix
				$cssContent = str_replace(
					array($appendBefore . '"' . $appendBefore, $appendBefore . "'" . $appendBefore),
					$appendBefore,
					$cssContent
				);

				// Bug Fix 2
				$cssContent = str_replace($appendBefore . 'http', 'http', $cssContent);
				$cssContent = str_replace($appendBefore . '//', '//', $cssContent);
			}
		}

		return $cssContent;
	}

	/**
	 * Next: Alter the HTML source by updating the original link URLs with the just cached ones
	 *
	 * @param $htmlSource
	 *
	 * @return mixed
	 */
	public static function updateHtmlSourceOriginalToOptimizedCss($htmlSource)
	{
		$cssOptimizeList = wp_cache_get('wpacu_css_optimize_list') ?: array();
		$allEnqueuedCleanLinkHrefs = wp_cache_get('wpacu_css_enqueued_hrefs') ?: array();

		preg_match_all('#<link[^>]*(stylesheet|(as(\s+|)=(\s+|)(|"|\')style(|"|\')))[^>]*(>)#Umi', OptimizeCommon::cleanerHtmlSource($htmlSource), $matchesSourcesFromTags, PREG_SET_ORDER);

		if (empty($matchesSourcesFromTags)) {
			return $htmlSource;
		}

		foreach ($matchesSourcesFromTags as $matches) {
			$linkSourceTag = $matches[0];

			if (strip_tags($linkSourceTag) !== '') {
				// Hmm? Not a valid tag... Skip it...
				continue;
			}

			// Is it a local CSS? Check if it's hardcoded (not enqueued the WordPress way)
			if ($cleanLinkHrefFromTagArray = OptimizeCommon::getLocalCleanSourceFromTag($linkSourceTag, 'href')) {
				$cleanLinkHrefFromTag = $cleanLinkHrefFromTagArray['source'];
				$afterQuestionMark = $cleanLinkHrefFromTagArray['after_question_mark'];

				if (! in_array($cleanLinkHrefFromTag, $allEnqueuedCleanLinkHrefs)) {
					// Not in the final enqueued list? Most likely hardcoded (not added via wp_enqueue_scripts())
					// Emulate the object value (as the enqueued styles)
					$value = (object)array(
						'handle' => md5($cleanLinkHrefFromTag),
						'src'    => $cleanLinkHrefFromTag,
						'ver'    => md5($afterQuestionMark)
					);

					$optimizeValues = self::maybeOptimizeIt($value);

					if (! empty($optimizeValues)) {
						$cssOptimizeList[] = $optimizeValues;
					}
				}
			}

			if (empty($cssOptimizeList)) {
				continue;
			}

			foreach ($cssOptimizeList as $listValues) {
				// The contents of the CSS file has been changed and thus, we will replace the source path from LINK with the cached (e.g. minified) one

				// If the minified files are deleted (e.g. /wp-content/cache/ is cleared)
				// do not replace the CSS file path to avoid breaking the website
				if (! file_exists(rtrim(ABSPATH, '/') . $listValues[1])) {
					continue;
				}

				$sourceUrl   = site_url() . $listValues[0];
				$optimizeUrl = site_url() . $listValues[1];

				if ($linkSourceTag !== str_ireplace($sourceUrl, $optimizeUrl, $linkSourceTag)) {
					$newLinkSourceTag = self::updateOriginalToOptimizedTag($linkSourceTag, $sourceUrl, $optimizeUrl);
					$htmlSource = str_replace($linkSourceTag, $newLinkSourceTag, $htmlSource);
					break;
				}
			}
		}

		return $htmlSource;
	}

	/**
	 * @param $linkSourceTag
	 * @param $sourceUrl
	 * @param $optimizeUrl
	 *
	 * @return mixed
	 */
	public static function updateOriginalToOptimizedTag($linkSourceTag, $sourceUrl, $optimizeUrl)
	{
		$newLinkSourceTag = str_replace($sourceUrl, $optimizeUrl, $linkSourceTag);

		// Needed in case it's added to the Combine CSS exceptions list
		if (CombineCss::proceedWithCssCombine()) {
			$newLinkSourceTag = str_ireplace('<link ', '<link data-wpacu-link-href-before="'.$sourceUrl.'" ', $newLinkSourceTag);
		}

		// Strip ?ver=
		$newLinkSourceTag = str_replace('.css&#038;ver=', '.css?ver=', $newLinkSourceTag);
		$toStrip = Misc::extractBetween($newLinkSourceTag, '?ver=', ' ');

		if (in_array(substr($toStrip, -1), array('"', "'"))) {
			$toStrip = '?ver='. trim(trim($toStrip, '"'), "'");
			$newLinkSourceTag = str_replace($toStrip, '', $newLinkSourceTag);
		}

		return $newLinkSourceTag;
	}

	/**
	 * @return bool
	 */
	public static function isInlineCssEnabled()
	{
		$isEnabledInSettingsWithList = (Main::instance()->settings['inline_css_files'] && trim(Main::instance()->settings['inline_css_files_list']) !== '');

		if (! $isEnabledInSettingsWithList) {
			return false;
		}

		// Finally, return true
		return true;
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed
	 */
	public static function doInline($htmlSource)
	{
		$minifyInlineTags = (! Main::instance()->preventAssetsSettings() && Main::instance()->settings['minify_loaded_css'] && Main::instance()->settings['minify_loaded_css_inline']);
		$allPatterns = self::getAllInlineChosenPatterns();

		// No patterns added? Return the same $htmlSource
		if (empty($allPatterns)) {
			return $htmlSource;
		}

		preg_match_all('#<link[^>]*stylesheet[^>]*('. implode('|', $allPatterns). ').*(>)#Usmi', $htmlSource, $matchesSourcesFromTags, PREG_SET_ORDER);

		if (! empty($matchesSourcesFromTags)) {
			foreach ($matchesSourcesFromTags as $matchList) {
				$matchedTag = $matchList[0];

				if (strip_tags($matchedTag) !== '') {
					continue; // something is funny, don't mess with the HTML alteration, leave it as it was
				}

				// Is there a media attribute? Make sure to add it to the STYLE tag
				preg_match_all('#media=(["\'])' . '(.*)' . '(["\'])#Usmi', $matchedTag, $outputMatchesMedia);
				$mediaAttrValue = isset($outputMatchesMedia[2][0]) ? trim($outputMatchesMedia[2][0], '"\'') : '';
				$mediaAttr = ($mediaAttrValue && $mediaAttrValue !== 'all') ? 'media=\''.$mediaAttrValue.'\'' : '';

				preg_match_all('#href=(["\'])' . '(.*)' . '(["\'])#Usmi', $matchedTag, $outputMatchesHref);
				$linkHrefOriginal = trim($outputMatchesHref[2][0], '"\'');
				$localAssetPath = OptimizeCommon::getLocalAssetPath($linkHrefOriginal, 'css');

				if (! $localAssetPath) {
					continue; // Not on the same domain
				}

				$cssContent = self::maybeFixCssContent(
					FileSystem::file_get_contents($localAssetPath), // CSS content
					OptimizeCommon::getPathToAssetDir($linkHrefOriginal) . '/'
				);

				// Move any @imports to top; This also strips any @imports to Google Fonts if the option is chosen
				$cssContent = self::importsUpdate($cssContent);

				if ($minifyInlineTags) {
					$cssContent = MinifyCss::applyMinification($cssContent);
				}

				$htmlSource = str_replace($matchedTag, '<style type=\'text/css\' '.$mediaAttr.' data-wpacu-inline-css-file=\'1\'>'."\n".$cssContent."\n".'</style>', $htmlSource);
			}
		}

		return $htmlSource;
	}

	/**
	 * Source: https://www.minifier.org/ | https://github.com/matthiasmullie/minify
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public static function importsUpdate($content)
	{
		if (preg_match_all('/(;?)(@import (?<url>url\()?(?P<quotes>["\']?).+?(?P=quotes)(?(url)\)));?/', $content, $matches)) {
			// Remove from content (they will be appended to the top if they qualify)
			foreach ($matches[0] as $import) {
				$content = str_replace($import, '', $content);
			}

			// Strip any @imports to Google Fonts if it's the case
			$importsAddToTop = Main::instance()->settings['google_fonts_remove'] ? FontsGoogleRemove::stripGoogleApisImport($matches[2]) : $matches[2];

			// Add to top if there are any imports left
			if (! empty($importsAddToTop)) {
				$content = implode(';', $importsAddToTop) . ';' . trim($content, ';');
			}
		}

		return $content;
	}

	/**
	 * @param string $returnType
	 *
	 * @return array|bool
	 */
	public static function isOptimizeCssEnabledByOtherParty($returnType = 'list')
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

		$cssOptimizeEnabledIn = array();

		foreach ($pluginsToCheck as $plugin => $pluginTitle) {
			// "Autoptimize" check
			if ($plugin === 'autoptimize/autoptimize.php' && Misc::isPluginActive($plugin) && get_option('autoptimize_css')) {
				$cssOptimizeEnabledIn[] = $pluginTitle;

				if ($returnType === 'if_enabled') { return true; }
			}

			// "WP Rocket" check
			if ($plugin === 'wp-rocket/wp-rocket.php' && Misc::isPluginActive($plugin)) {
				if (function_exists('get_rocket_option')) {
					$wpRocketMinifyCss = trim(get_rocket_option('minify_css')) ?: false;
					$wpRocketMinifyConcatenateCss = trim(get_rocket_option('minify_concatenate_css')) ?: false;
				} else {
					$wpRocketSettings  = get_option('wp_rocket_settings');
					$wpRocketMinifyCss = isset($wpRocketSettings['minify_css']) && trim($wpRocketSettings['minify_css']);
					$wpRocketMinifyConcatenateCss = isset($wpRocketSettings['minify_concatenate_css']) && trim($wpRocketSettings['minify_concatenate_css']);
				}

				if ($wpRocketMinifyCss || $wpRocketMinifyConcatenateCss) {
					$cssOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "WP Fastest Cache" check
			if ($plugin === 'wp-fastest-cache/wpFastestCache.php' && Misc::isPluginActive($plugin)) {
				$wpfcOptionsJson = get_option('WpFastestCache');
				$wpfcOptions = @json_decode($wpfcOptionsJson, ARRAY_A);

				if (isset($wpfcOptions['wpFastestCacheMinifyCss']) || isset($wpfcOptions['wpFastestCacheCombineCss'])) {
					$cssOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "W3 Total Cache" check
			if ($plugin === 'w3-total-cache/w3-total-cache.php' && Misc::isPluginActive($plugin)) {
				$w3tcConfigMaster = Misc::getW3tcMasterConfig();
				$w3tcEnableCss = (int)trim(Misc::extractBetween($w3tcConfigMaster, '"minify.css.enable":', ','), '" ');

				if ($w3tcEnableCss === 1) {
					$cssOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "SG Optimizer" check
			if ($plugin === 'sg-cachepress/sg-cachepress.php' && Misc::isPluginActive($plugin)) {
				if (class_exists('\SiteGround_Optimizer\Options\Options')
				    && method_exists('\SiteGround_Optimizer\Options\Options', 'is_enabled')
				    && @\SiteGround_Optimizer\Options\Options::is_enabled('siteground_optimizer_combine_css')) {
					$cssOptimizeEnabledIn[] = $pluginTitle;
					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "Fast Velocity Minify" check
			if ($plugin === 'fast-velocity-minify/fvm.php' && Misc::isPluginActive($plugin)) {
				// It's enough if it's active due to its configuration
				$cssOptimizeEnabledIn[] = $pluginTitle;

				if ($returnType === 'if_enabled') { return true; }
			}

			// "LiteSpeed Cache" check
			if ($plugin === 'litespeed-cache/litespeed-cache.php' && Misc::isPluginActive($plugin) && ($liteSpeedCacheConf = apply_filters('litespeed_cache_get_options', get_option('litespeed-cache-conf')))) {
				if ( (isset($liteSpeedCacheConf['css_minify']) && $liteSpeedCacheConf['css_minify'])
				     || (isset($liteSpeedCacheConf['css_combine']) && $liteSpeedCacheConf['css_combine']) ) {
					$cssOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}

			// "Swift Performance Lite" check
			if ($plugin === 'swift-performance-lite/performance.php' && Misc::isPluginActive($plugin)
			    && class_exists('Swift_Performance_Lite') && method_exists('Swift_Performance_Lite', 'check_option')) {
				if ( @\Swift_Performance_Lite::check_option('merge-styles', 1) ) {
					$cssOptimizeEnabledIn[] = $pluginTitle;
				}

				if ($returnType === 'if_enabled') { return true; }
			}

			// "Breeze – WordPress Cache Plugin"
			if ($plugin === 'breeze/breeze.php' && Misc::isPluginActive($plugin)) {
				$breezeBasicSettings    = get_option('breeze_basic_settings');
				$breezeAdvancedSettings = get_option('breeze_advanced_settings');

				if (isset($breezeBasicSettings['breeze-minify-css'], $breezeAdvancedSettings['breeze-group-css'])
				    && $breezeBasicSettings['breeze-minify-css'] && $breezeAdvancedSettings['breeze-group-css']) {
					$cssOptimizeEnabledIn[] = $pluginTitle;

					if ($returnType === 'if_enabled') { return true; }
				}
			}
		}

		if ($returnType === 'if_enabled') { return false; }

		return $cssOptimizeEnabledIn;
	}

	/**
	 * @return bool
	 */
	public static function isWpRocketOptimizeCssDeliveryEnabled()
	{
		if (Misc::isPluginActive('wp-rocket/wp-rocket.php')) {
			if (function_exists('get_rocket_option')) {
				$wpRocketAsyncCss = trim(get_rocket_option('async_css')) ?: false;
			} else {
				$wpRocketSettings  = get_option('wp_rocket_settings');
				$wpRocketAsyncCss = isset($wpRocketSettings['async_css']) && trim($wpRocketSettings['async_css']);
			}

			return $wpRocketAsyncCss;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function wpfcMinifyCssEnabledOnly()
	{
		if (Misc::isPluginActive('wp-fastest-cache/wpFastestCache.php')) {
			$wpfcOptionsJson = get_option('WpFastestCache');
			$wpfcOptions     = @json_decode($wpfcOptionsJson, ARRAY_A);

			// "Minify CSS" is enabled, "Combine CSS" is disabled
			return isset($wpfcOptions['wpFastestCacheMinifyCss']) && ! isset($wpfcOptions['wpFastestCacheCombineCss']);
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isWorthCheckingForOptimization()
	{
		// At least one of these options have to be enabled
		// Otherwise, we will not perform specific useless actions and save resources
		// [wpacu_lite]
		return MinifyCss::isMinifyCssEnabled() ||
		       Main::instance()->settings['google_fonts_display'] ||
		       Main::instance()->settings['google_fonts_remove'];
		// [/wpacu_lite]
	}

	}
