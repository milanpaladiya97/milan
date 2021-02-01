<?php
namespace WpAssetCleanUp\OptimiseAssets;

use WpAssetCleanUp\CleanUp;
use WpAssetCleanUp\FileSystem;
use WpAssetCleanUp\Main;
use WpAssetCleanUp\Menu;
use WpAssetCleanUp\Misc;
use WpAssetCleanUp\Plugin;
use WpAssetCleanUp\Tools;

/**
 * Class OptimizeCommon
 * @package WpAssetCleanUp
 */
class OptimizeCommon
{
	/**
	 * @var string
	 */
	public static $relPathPluginCacheDirDefault = '/cache/asset-cleanup/'; // keep forward slash at the end

	/**
	 * @var string
	 */
	public static $optimizedSingleFilesDir = 'item';

	/**
	 * @var array
	 */
	public static $wellKnownExternalHosts = array(
		'googleapis.com',
		'bootstrapcdn.com',
		'cloudflare.com',
		'jsdelivr.net'
	);

	/**
	 *
	 */
	public function init()
	{
		add_action('switch_theme', array($this, 'clearAllCache'));
		add_action('after_switch_theme', array($this, 'clearAllCache'));

		// Is WP Rocket's page cache cleared? Clear Asset CleanUp's CSS cache files too
		if (array_key_exists('action', $_GET) && $_GET['action'] === 'purge_cache') {
			// Leave its default parameters, no redirect needed
			add_action('init', static function() {
				OptimizeCommon::clearAllCache();
			}, PHP_INT_MAX);
		}

		add_action('admin_post_assetcleanup_clear_assets_cache', function() {
			self::clearAllCache(true);
		});

		add_action('wp_loaded', array($this, 'alterHtmlSource'), 1);
	}

	/**
	 *
	 */
	public function alterHtmlSource()
	{
		if (is_admin()) { // don't apply any changes if not in the front-end view (e.g. Dashboard view)
			return;
		}

		ob_start(static function($htmlSource) {
			// Do not do any optimization if "Test Mode" is Enabled
			if (! Menu::userCanManageAssets() && Main::instance()->settings['test_mode']) {
				return $htmlSource;
			}

			$htmlSource = OptimizeCss::alterHtmlSource($htmlSource);
			$htmlSource = OptimizeJs::alterHtmlSource($htmlSource);

			$htmlSource = Main::instance()->settings['remove_generator_tag'] ? CleanUp::removeMetaGenerators($htmlSource) : $htmlSource;
			$htmlSource = Main::instance()->settings['remove_html_comments'] ? CleanUp::removeHtmlComments($htmlSource) : $htmlSource;

			if (in_array(Main::instance()->settings['disable_xmlrpc'], array('disable_all', 'disable_pingback'))) {
				// Also clean it up from the <head> in case it's hardcoded
				$htmlSource = CleanUp::cleanPingbackLinkRel($htmlSource);
			}

			return $htmlSource;
		});
	}

	/**
	 * @return string
	 */
	public static function getRelPathPluginCacheDir()
	{
		// In some cases, hosting companies put restriction for writable folders
		// Pantheon, for instance, allows only /wp-content/uploads/ to be writable
		// For security reasons, do not allow ../
		return ((defined('WPACU_CACHE_DIR') && strpos(WPACU_CACHE_DIR, '../') === false)
			? WPACU_CACHE_DIR
			: self::$relPathPluginCacheDirDefault);
	}

	/**
	 * The following output is used only for fetching purposes
	 * It will not be part of the final output
	 *
	 * @param $htmlSource
	 *
	 * @return string|string[]|null
	 */
	public static function cleanerHtmlSource($htmlSource)
	{
		// Removes HTML comments including MSIE conditional ones as they are left intact
		// and not combined with other JavaScript files in case the method is called from CombineJs.php
		return preg_replace('/<!--(.|\s)*?-->/', '', $htmlSource);
	}

	/**
	 * Is this a regular WordPress page (not feed, REST API etc.)?
	 * If not, do not proceed with any CSS/JS combine
	 *
	 * @return bool
	 */
	public static function doCombineIsRegularPage()
	{
		// In particular situations, do not process this
		if (strpos($_SERVER['REQUEST_URI'], '/wp-content/plugins/') !== false
		    && strpos($_SERVER['REQUEST_URI'], '/wp-content/themes/') !== false) {
			return false;
		}

		if (Misc::endsWith($_SERVER['REQUEST_URI'], '/comments/feed/')) {
			return false;
		}

		if (str_replace('//', '/', site_url() . '/feed/') === $_SERVER['REQUEST_URI']) {
			return false;
		}

		if (is_feed()) { // any kind of feed page
			return false;
		}

		return true;
	}

	/**
	 * @param $href
	 * @param $assetType
	 *
	 * @return bool|string
	 */
	public static function getLocalAssetPath($href, $assetType)
	{
		$href = self::isSourceFromSameHost($href);

		if (! $href) {
			return false;
		}

		$hrefRelPath = self::getHrefRelPath($href);

		if (strpos($hrefRelPath, '/') === 0) {
			$hrefRelPath = substr($hrefRelPath, 1);
		}

		$localAssetPath = ABSPATH . $hrefRelPath;

		if (strpos($localAssetPath, '?ver=') !== false) {
			list($localAssetPathAlt,) = explode('?ver=', $localAssetPath);
			$localAssetPath = $localAssetPathAlt;
		}

		// Not using "?ver="
		if (strpos($localAssetPath, '.' . $assetType . '?') !== false) {
			list($localAssetPathAlt,) = explode('.' . $assetType . '?', $localAssetPath);
			$localAssetPath = $localAssetPathAlt . '.' . $assetType;
		}

		if (strrchr($localAssetPath, '.') === '.' . $assetType && file_exists($localAssetPath)) {
			return $localAssetPath;
		}

		return false;
	}

	/**
	 * @param $assetHref
	 *
	 * @return bool|mixed|string
	 */
	public static function getPathToAssetDir($assetHref)
	{
		$posLastSlash   = strrpos($assetHref, '/');
		$pathToAssetDir = substr($assetHref, 0, $posLastSlash);

		$parseUrl = parse_url($pathToAssetDir);

		if (isset($parseUrl['scheme']) && $parseUrl['scheme'] !== '') {
			$pathToAssetDir = str_replace(
				array('http://'.$parseUrl['host'], 'https://'.$parseUrl['host']),
				'',
				$pathToAssetDir
			);
		} elseif (strpos($pathToAssetDir, '//') === 0) {
			$pathToAssetDir = str_replace(
				array('//'.$parseUrl['host'], '//'.$parseUrl['host']),
				'',
				$pathToAssetDir
			);
		}

		return $pathToAssetDir;
	}

	/**
	 * @param $sourceTag
	 * @param string $forAttr
	 *
	 * @return array|bool
	 */
	public static function getLocalCleanSourceFromTag($sourceTag, $forAttr)
	{
		preg_match_all('#'.$forAttr.'=(["\'])' . '(.*)' . '(["\'])#Usmi', $sourceTag, $outputMatchesSource);

		$sourceFromTag = (isset($outputMatchesSource[2][0]) && $outputMatchesSource[2][0])
			? trim($outputMatchesSource[2][0], '"\'')
			: false;

		if ($sourceFromTag && stripos($sourceFromTag, site_url()) !== false) {
			$cleanLinkHrefFromTag = trim($sourceFromTag, '?&');
			$afterQuestionMark = WPACU_PLUGIN_VERSION;

			// Is it a dynamic URL? Keep the full path
			if (strpos($cleanLinkHrefFromTag, '/?') !== false || strpos($cleanLinkHrefFromTag, '.php') !== false) {
				list(,$afterQuestionMark) = explode('?', $sourceFromTag);
			} elseif (strpos($sourceFromTag, '?') !== false) {
				list($cleanLinkHrefFromTag, $afterQuestionMark) = explode('?', $sourceFromTag);
			}

			if (! $afterQuestionMark) {
				return false;
			}

			return array('source' => $cleanLinkHrefFromTag, 'after_question_mark' => $afterQuestionMark);
		}

		return false;
	}

	/**
	 * @param $href
	 *
	 * @return bool
	 */
	public static function isSourceFromSameHost($href)
	{
		// Check the host name
		$siteDbUrl   = get_option('siteurl');
		$siteUrlHost = strtolower(parse_url($siteDbUrl, PHP_URL_HOST));

		if (strpos($href, '//') === 0) {
			list ($urlPrefix) = explode('//', $siteDbUrl);
			$href = $urlPrefix . $href;
		}

		/*
		 * Validate it first
		 */
		$assetHost = strtolower(parse_url($href, PHP_URL_HOST));

		if (preg_match('#'.$assetHost.'#si', implode('', self::$wellKnownExternalHosts))) {
			return false;
		}

		// Different host name (most likely 3rd party one such as fonts.googleapis.com or an external CDN)
		// Do not add it to the combine list
		if ($assetHost !== $siteUrlHost) {
			return false;
		}

		return $href;
	}

	/**
	 * @param $href
	 *
	 * @return mixed
	 */
	public static function getHrefRelPath($href)
	{
		$parseUrl = parse_url($href);
		$hrefHost = isset($parseUrl['host']) ? $parseUrl['host'] : false;

		if (! $hrefHost) {
			return $href;
		}

		// Sometimes host is different on Staging websites such as the ones from Siteground
		// e.g. staging1.domain.com and domain.com
		// We need to make sure that the URI path is fetched correctly based on the host value from the $href
		$siteDbUrl      = get_option('siteurl');
		$parseDbSiteUrl = parse_url($siteDbUrl);

		$dbSiteUrlHost = $parseDbSiteUrl['host'];

		$finalBaseUrl = str_replace($dbSiteUrlHost, $hrefHost, $siteDbUrl);

		return str_replace($finalBaseUrl, '', $href);
	}

	/**
	 * @param $jsonStorageFile
	 * @param $relPathAssetCacheDir
	 * @param $assetType
	 * @param $forType
	 *
	 * @return array|mixed|object
	 */
	public static function getAssetCachedData($jsonStorageFile, $relPathAssetCacheDir, $assetType, $forType = 'combine')
	{
		if ($forType === 'combine') {
			// Only clean request URIs allowed
			if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
				list($requestUri) = explode('?', $_SERVER['REQUEST_URI']);
			} else {
				$requestUri = $_SERVER['REQUEST_URI'];
			}

			$requestUriPart = $requestUri;

			if ($requestUri === '/' || is_404()) {
				$requestUriPart = '';
			}

			$dirToFilename = WP_CONTENT_DIR . dirname($relPathAssetCacheDir) . '/_storage/'
			                 . parse_url(site_url(), PHP_URL_HOST) .
			                 $requestUriPart . '/';

			$dirToFilename = str_replace('//', '/', $dirToFilename);

			$assetsFile = $dirToFilename . self::filterStorageFileName($jsonStorageFile);
		} elseif ($forType === 'item') {
			$dirToFilename = WP_CONTENT_DIR . dirname($relPathAssetCacheDir) . '/_storage/'.self::$optimizedSingleFilesDir.'/';
			$assetsFile = $dirToFilename . $jsonStorageFile;
		}

		if (! file_exists($assetsFile)) {
			return array();
		}

		if ($assetType === 'css') {
			$cachedAssetsFileExpiresIn = OptimizeCss::$cachedCssAssetsFileExpiresIn;
		} elseif ($assetType === 'js') {
			$cachedAssetsFileExpiresIn = OptimizeJs::$cachedJsAssetsFileExpiresIn;
		} else {
			return array();
		}

		// Delete cached file after it expired as it will be regenerated
		if (filemtime($assetsFile) < (time() - 1 * $cachedAssetsFileExpiresIn)) {
			self::clearAssetCachedData($jsonStorageFile);
			return array();
		}

		$optionValue = FileSystem::file_get_contents($assetsFile);

		if ($optionValue) {
			$optionValueArray = @json_decode($optionValue, ARRAY_A);

			if ($forType === 'combine') {
				if ($assetType === 'css' && (! empty($optionValueArray) && (isset($optionValueArray['head']['link_hrefs']) || isset($optionValueArray['body']['link_hrefs'])))) {
					return $optionValueArray;
				}

				if ($assetType === 'js' && ! empty($optionValueArray)) {
					return $optionValueArray;
				}
			} elseif ($forType === 'item') {
				return $optionValueArray;
			}
		}

		// File exists, but it's invalid or outdated; Delete it as it has to be re-generated
		self::clearAssetCachedData($jsonStorageFile);

		return array();
	}

	/**
	 * @param $jsonStorageFile
	 * @param $relPathAssetCacheDir
	 * @param $list
	 * @param $forType
	 */
	public static function setAssetCachedData($jsonStorageFile, $relPathAssetCacheDir, $list, $forType = 'combine')
	{
		// Combine CSS/JS JSON Storage
		if ($forType === 'combine') {
			// Only clean request URIs allowed
			if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
				list($requestUri) = explode('?', $_SERVER['REQUEST_URI']);
			} else {
				$requestUri = $_SERVER['REQUEST_URI'];
			}

			$requestUriPart = $requestUri;

			if ($requestUri === '/' || is_404()) {
				$requestUriPart = '';
			}

			$dirToFilename = WP_CONTENT_DIR . dirname($relPathAssetCacheDir) . '/_storage/'
			                 . parse_url(site_url(), PHP_URL_HOST) .
			                 $requestUriPart . '/';

			$dirToFilename = str_replace('//', '/', $dirToFilename);

			if (! is_dir($dirToFilename)) {
				$makeFileDir = @mkdir($dirToFilename, 0755, true);

				if (! $makeFileDir) {
					return;
				}
			}

			$assetsFile = $dirToFilename . self::filterStorageFileName($jsonStorageFile);

			// CSS/JS JSON FILE DATA
			$assetsValue = $list;
		}

		// Optimize single CSS/JS item JSON Storage
		if ($forType === 'item') {
			$dirToFilename = WP_CONTENT_DIR . dirname($relPathAssetCacheDir) . '/_storage/'.self::$optimizedSingleFilesDir.'/';

			$dirToFilename = str_replace('//', '/', $dirToFilename);

			if (! is_dir($dirToFilename)) {
				$makeFileDir = @mkdir($dirToFilename, 0755, true);

				if (! $makeFileDir) {
					return;
				}
			}

			$assetsFile = $dirToFilename . $jsonStorageFile;
			$assetsValue = $list;
		}

		FileSystem::file_put_contents($assetsFile, $assetsValue);
	}

	/**
	 * @param $jsonStorageFile
	 */
	public static function clearAssetCachedData($jsonStorageFile)
	{
		if (strpos($jsonStorageFile, '-combined') !== false) {
			/*
	        * #1: Combined CSS/JS JSON
	        */
			// Only clean request URIs allowed
			if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
				list($requestUri) = explode('?', $_SERVER['REQUEST_URI']);
			} else {
				$requestUri = $_SERVER['REQUEST_URI'];
			}

			$requestUriPart = $requestUri;

			if ($requestUri === '/' || is_404()) {
				$requestUriPart = '';
			}

			$dirToFilename = WP_CONTENT_DIR . self::getRelPathPluginCacheDir() . '_storage/'
			                 . parse_url(site_url(), PHP_URL_HOST) .
			                 $requestUriPart;

			// If it doesn't have "/" at the end, append it (it will prevent double forward slashes)
			if (substr($dirToFilename, - 1) !== '/') {
				$dirToFilename .= '/';
			}

			$assetsFile = $dirToFilename . self::filterStorageFileName($jsonStorageFile);
		} elseif (strpos($jsonStorageFile, '_optimize_') !== false) {
			/*
			 * #2: Optimized CSS/JS JSON
			 */
			$dirToFilename = WP_CONTENT_DIR . self::getRelPathPluginCacheDir() . '_storage/'.self::$optimizedSingleFilesDir.'/';
			$assetsFile = $dirToFilename . $jsonStorageFile;
		}

		if (file_exists($assetsFile)) { // avoid E_WARNING errors | check if it exists first
			@unlink($assetsFile);
		}
	}

	/**
	 * Clears all CSS & JS cache
	 *
	 * @param bool $redirectAfter
	 */
	public static function clearAllCache($redirectAfter = false)
	{
		if (self::doNotClearAllCache()) {
			return;
		}

		/*
		 * STEP 1: Clear all .json, .css & .js files (older than $clearFilesOlderThan days) that are related to "Minify/Combine CSS/JS files" feature
		 */
		$skipFiles       = array('index.php', '.htaccess');
		$fileExtToRemove = array('.json', '.css', '.js');

		$clearFilesOlderThan = Main::instance()->settings['clear_cached_files_after']; // days

		$assetCleanUpCacheDir = WP_CONTENT_DIR . self::getRelPathPluginCacheDir();
		$storageDir           = $assetCleanUpCacheDir . '_storage';

		$userIdDirs = array();

		if (is_dir($assetCleanUpCacheDir)) {
			$storageEmptyDirs = $allJsons = $allAssets = $allAssetsToKeep = array();

			$dirItems = new \RecursiveDirectoryIterator($assetCleanUpCacheDir, \RecursiveDirectoryIterator::SKIP_DOTS);

			foreach (new \RecursiveIteratorIterator($dirItems, \RecursiveIteratorIterator::SELF_FIRST) as $item) {
				$fileBaseName = trim(strrchr($item, '/'), '/');
				$fileExt = strrchr($fileBaseName, '.');

				if (is_file($item) && in_array($fileExt, $fileExtToRemove) && (! in_array($fileBaseName, $skipFiles))) {
					$isJsonFile  = ($fileExt === '.json');
					$isAssetFile = in_array($fileExt, array('.css', '.js'));

					// Remove all JSONs and .css & .js ONLY if they are older than $clearFilesOlderThan
					if ($isJsonFile || ($isAssetFile && (strtotime('-' . $clearFilesOlderThan . ' days') > $item->getCTime()))) {
						if ($isJsonFile) {
							$allJsons[] = $item;
						}

						if ($isAssetFile) {
							$allAssets[] = $item;
						}
					}
				} elseif (is_dir($item) && (strpos($item, '/css/logged-in/') !== false || strpos($item, '/js/logged-in/') !== false)) {
					$userIdDirs[] = $item;
				} elseif ($item != $storageDir && strpos($item, $storageDir) !== false) {
					$storageEmptyDirs[] = $item;
				}
			}

			// Now go through the JSONs and collect the latest assets so they would be kept
			foreach ($allJsons as $jsonFile) {
				$jsonContents = FileSystem::file_get_contents($jsonFile);
				$jsonContentsArray = @json_decode($jsonContents, ARRAY_A);

				$uriToFinalCssFileIndexKey = 'uri_to_final_css_file';
				$uriToFinalJsFileIndexKey = 'uri_to_final_js_file';

				if (is_array($jsonContentsArray) && strpos($jsonContents, $uriToFinalCssFileIndexKey) !== false) {
					if (isset($jsonContentsArray['head'][$uriToFinalCssFileIndexKey])) {
						$allAssetsToKeep[] = WP_CONTENT_DIR . OptimizeCss::getRelPathCssCacheDir() . $jsonContentsArray['head'][$uriToFinalCssFileIndexKey];
					}

					if (isset($jsonContentsArray['body'][$uriToFinalCssFileIndexKey])) {
						$allAssetsToKeep[] = WP_CONTENT_DIR . OptimizeCss::getRelPathCssCacheDir() . $jsonContentsArray['body'][$uriToFinalCssFileIndexKey];
					}
				} elseif (is_array($jsonContentsArray) && strpos($jsonContents, $uriToFinalJsFileIndexKey) !== false) {
					foreach ($jsonContentsArray as $jsGroupVal) {
						if (isset($jsGroupVal[$uriToFinalJsFileIndexKey]) ) {
							$allAssetsToKeep[] = WP_CONTENT_DIR . OptimizeJs::getRelPathJsCacheDir() . $jsGroupVal[$uriToFinalJsFileIndexKey];
						}
					}
				}

				// Clear the JSON files as new ones will be generated
				@unlink($jsonFile);
			}

			// Finally, collect the rest of $allAssetsToKeep from the database transients
			// Do not check if they are expired or not as their assets could still be referenced
			// until those pages will be accessed in a non-cached way
			global $wpdb;

			$sqlGetCacheTransients = <<<SQL
SELECT option_value FROM `{$wpdb->options}` 
WHERE `option_name` LIKE '%transient_wpacu_css_optimize%' OR `option_name` LIKE '%transient_wpacu_js_optimize%'
SQL;
			$cacheTransients = $wpdb->get_col($sqlGetCacheTransients);

			if (! empty($cacheTransients)) {
				foreach ($cacheTransients as $optionValue) {
					$jsonValueArray = @json_decode($optionValue, ARRAY_A);

					if (isset($jsonValueArray['optimize_uri'])) {
						$allAssetsToKeep[] = rtrim(ABSPATH, '/') . $jsonValueArray['optimize_uri'];
					}
				}
			}

			// Finally clear the matched assets, except the active ones
			foreach ($allAssets as $assetFile) {
				if (in_array($assetFile, $allAssetsToKeep)) {
					continue;
				}
				@unlink($assetFile);
			}

			foreach (array_reverse($storageEmptyDirs) as $storageEmptyDir) {
				@rmdir($storageEmptyDir);
			}

			// Remove empty dirs from /css/logged-in/ and /js/logged-in/
			if (! empty($userIdDirs)) {
				foreach ($userIdDirs as $userIdDir) {
					@rmdir($userIdDir); // it needs to be empty, otherwise, it will not be removed
				}
			}
		}

		//removeIf(develoment)
			// Remove "min" and "one" directories (created with Pro Version < 1.1.3.8 | Lite Version < 1.3.3.7) if they are empty
		//endRemoveIf(develoment)
		@rmdir(WP_CONTENT_DIR . OptimizeCss::getRelPathCssCacheDir().'min');
		@rmdir(WP_CONTENT_DIR . OptimizeJs::getRelPathJsCacheDir().'min');
		@rmdir(WP_CONTENT_DIR . OptimizeCss::getRelPathCssCacheDir().'one');
		@rmdir(WP_CONTENT_DIR . OptimizeJs::getRelPathJsCacheDir().'one');

		/*
		 * STEP 2: Remove all transients related to the Minify CSS/JS files feature
		 */
		$toolsClass = new Tools();
		$toolsClass->clearAllCacheTransients();

		// Make sure all the caching files/folders are there in case the plugin was upgraded
		Plugin::createCacheFoldersFiles(array('css', 'js'));

		if ($redirectAfter && wp_get_referer()) {
			wp_safe_redirect(wp_get_referer());
			exit;
		}
	}

	/**
	 * @return array
	 */
	public static function getStorageStats()
	{
		$assetCleanUpCacheDir = WP_CONTENT_DIR . self::getRelPathPluginCacheDir();

		if (is_dir($assetCleanUpCacheDir)) {
			$dirItems = new \RecursiveDirectoryIterator($assetCleanUpCacheDir, \RecursiveDirectoryIterator::SKIP_DOTS);

			$totalFiles = 0;
			$totalSize = 0;

			foreach (new \RecursiveIteratorIterator($dirItems, \RecursiveIteratorIterator::SELF_FIRST) as $item) {
				$fileBaseName = trim(strrchr($item, '/'), '/');
				$fileExt = strrchr($fileBaseName, '.');

				if ($item->isFile() && in_array($fileExt, array('.css', '.js'))) {
					$totalSize += $item->getSize();
					$totalFiles++;
				}
			}

			return array(
				'total_size'  => Misc::formatBytes($totalSize),
				'total_files' => $totalFiles
			);
		}

		return array();
	}

	/**
	 * Prevent clear cache function in the following situations
	 *
	 * @return bool
	 */
	public static function doNotClearAllCache()
	{
		// WooCommerce GET or AJAX call
		if (array_key_exists('wc-ajax', $_GET) && $_GET['wc-ajax']) {
			return true;
		}

		if (defined('WC_DOING_AJAX') && WC_DOING_AJAX === true) {
			return true;
		}

		return false;
	}

	/**
	 * @param $fileName
	 *
	 * @return mixed
	 */
	public static function filterStorageFileName($fileName)
	{
		$filterString = is_404() ? '-404-not-found' : '';

		$current_user = wp_get_current_user();

		if (isset($current_user->ID) && $current_user->ID > 0) {
			$fileName = str_replace(
				'{maybe-extra-info}',
				$filterString.'-logged-in',
				$fileName
			);
		} else {
			// Just clear {maybe-extra-info}
			$fileName = str_replace('{maybe-extra-info}', $filterString, $fileName);
		}

		return $fileName;
	}

	/**
	 * @return mixed|string
	 */
	public static function filterWpContentUrl()
	{
		$wpContentUrl = WP_CONTENT_URL;

		// Is the page loaded via SSL, but the site url from the database starts with 'http://'
		// Then use '//' in front of CSS/JS generated via Asset CleanUp
		if (Misc::isHttpsSecure() && strpos($wpContentUrl, 'http://') !== false) {
			$wpContentUrl = str_replace('http://', '//', $wpContentUrl);
		}

		return $wpContentUrl;
	}

	/**
	 * @param $assetContent
	 *
	 * @return mixed
	 */
	public static function stripSourceMap($assetContent)
	{
		return str_replace('# sourceMappingURL=', '# From Source Map: ', $assetContent);
	}

	/**
	 * URLs with query strings are not loading Optimised Assets (e.g. combine CSS files into one file)
	 * However, there are exceptions such as the ones below (preview, debugging purposes)
	 *
	 * @return bool
	 */
	public static function loadOptimizedAssetsIfQueryStrings()
	{
		$isPreview = (isset($_GET['preview_id'], $_GET['preview_nonce'], $_GET['preview']) || isset($_GET['preview']));
		$isQueryStringDebug = isset($_GET['wpacu_no_css_minify']) || isset($_GET['wpacu_no_js_minify']) || isset($_GET['wpacu_no_css_combine']) || isset($_GET['wpacu_no_js_combine']);

		return ($isPreview || $isQueryStringDebug);
	}

	/**
	 * @param $transient
	 * @param $fromLocation
	 *
	 * @return bool|mixed
	 */
	public static function getTransient($transient, $fromLocation = 'db')
	{
		$contents = '';

		// Local record
		if ($fromLocation === 'local') {
			$dirToFilename = WP_CONTENT_DIR . self::getRelPathPluginCacheDir() . '_storage/'.self::$optimizedSingleFilesDir.'/';
			$assetsFile = $dirToFilename . $transient.'.json';

			if (file_exists($assetsFile)) {
				$contents = trim(FileSystem::file_get_contents($assetsFile));

				if (! $contents) {
					// Empty file? Something weird, use the MySQL record as a fallback
					return get_transient($transient);
				}
			}

			return $contents;
		}

		// MySQL record: $fromLocation default 'db'
		return get_transient($transient);
	}

	/**
	 * @param $transientName
	 */
	public static function deleteTransient($transientName)
	{
		// MySQL record
		delete_transient($transientName);

		// File record
		self::clearAssetCachedData($transientName.'.json');
	}

	/**
	 * @param $transient
	 * @param $value
	 * @param int $expiration
	 */
	public static function setTransient($transient, $value, $expiration = 0)
	{
		// MySQL record
		set_transient($transient, $value, $expiration);

		// File record
		self::setAssetCachedData(
			$transient.'.json',
			OptimizeCss::getRelPathCssCacheDir(),
			$value,
			'item'
		);
	}
}
