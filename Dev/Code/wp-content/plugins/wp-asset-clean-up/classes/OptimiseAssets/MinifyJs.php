<?php
namespace WpAssetCleanUp\OptimiseAssets;

use WpAssetCleanUp\CleanUp;
use WpAssetCleanUp\Main;
use WpAssetCleanUp\Menu;
use WpAssetCleanUp\MetaBoxes;
use MatthiasMullie\Minify;

/**
 * Class MinifyJs
 * @package WpAssetCleanUp\OptimiseAssets
 */
class MinifyJs
{
	/**
	 * @param $jsContent
	 *
	 * @return string|string[]|null
	 */
	public static function applyMinification($jsContent)
	{
		$minifier = new Minify\JS($jsContent);
			return trim($minifier->minify());
		}

	/**
	 * @param $src
	 * @param string $handle
	 *
	 * @return bool
	 */
	public static function skipMinify($src, $handle = '')
	{
		// Things like WP Fastest Cache Toolbar JS shouldn't be minified and take up space on the server
		if ($handle !== '' && in_array($handle, Main::instance()->skipAssets['scripts'])) {
			return true;
		}

		$regExps = array(
			'#/wp-content/plugins/wp-asset-clean-up(.*?).min.js#',

			// Other libraries from the core that end in .min.js
			'#/wp-includes/(.*?).min.js#',

			// jQuery library
			'#/wp-includes/js/jquery/jquery.js#',

			// Files within /wp-content/uploads/
			// Files within /wp-content/uploads/ or /wp-content/cache/
			// Could belong to plugins such as "Elementor, "Oxygen" etc.
			'#/wp-content/uploads/(.*?).js#',
			'#/wp-content/cache/(.*?).js#',

			// Elementor .min.js
			'#/wp-content/plugins/elementor/assets/(.*?).min.js#',

			// WooCommerce Assets
			'#/wp-content/plugins/woocommerce/assets/js/(.*?).min.js#'

			);

		if (Main::instance()->settings['minify_loaded_js_exceptions'] !== '') {
			$loadedJsExceptionsPatterns = trim(Main::instance()->settings['minify_loaded_js_exceptions']);

			if (strpos($loadedJsExceptionsPatterns, "\n")) {
				// Multiple values (one per line)
				foreach (explode("\n", $loadedJsExceptionsPatterns) as $loadedJsExceptionPattern) {
					$regExps[] = '#'.trim($loadedJsExceptionPattern).'#';
				}
			} else {
				// Only one value?
				$regExps[] = '#'.trim($loadedJsExceptionsPatterns).'#';
			}
		}

		foreach ($regExps as $regExp) {
			if ( preg_match( $regExp, $src ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed|string
	 */
	public static function minifyInlineScriptTags($htmlSource)
	{
		if (stripos($htmlSource, '<script') === false) {
			return $htmlSource; // no SCRIPT tags, hmm
		}

		$domTag = new \DOMDocument();
		libxml_use_internal_errors(true);
		$domTag->loadHTML($htmlSource);

		$scriptTagsObj = $domTag->getElementsByTagName( 'script' );

		if ($scriptTagsObj === null) {
			return $htmlSource;
		}

		$skipTagsContaining = array_map( static function ( $toMatch ) {
			return preg_quote($toMatch, '/');
		}, array(
			'/* <![CDATA[ */', // added via wp_localize_script()
			'window._wpemojiSettings', // Emoji
			'wpacu-google-fonts-async-load',
			'wpacu-preload-async-css-fallback',
			'document.body.prepend(wpacuLinkTag',
			'var wc_product_block_data = JSON.parse( decodeURIComponent('
		));

		foreach ($scriptTagsObj as $scriptTagObj) {
			// Does it have the "src" attribute? Skip it as it's not an inline SCRIPT tag
			if (isset($scriptTagObj->attributes) && $scriptTagObj->attributes !== null) {
				foreach ($scriptTagObj->attributes as $attrObj) {
					if ($attrObj->nodeName === 'src') {
						continue 2;
					}
				}
			}

			$originalTag = CleanUp::getOuterHTML($scriptTagObj);

			// No need to use extra resources as the tag is already minified
			if (preg_match('/('.implode('|', $skipTagsContaining).')/', $originalTag)) {
				continue;
			}

			$originalTagContents = (isset($scriptTagObj->nodeValue) && trim($scriptTagObj->nodeValue) !== '') ? $scriptTagObj->nodeValue : false;

			if ($originalTagContents) {
				$newTagContents = self::applyMinification($originalTagContents);

				$htmlSource = str_ireplace('>'.$originalTagContents.'</script', '>'.$newTagContents.'</script', $htmlSource);

				libxml_clear_errors();
			}
		}

		return $htmlSource;
	}

	/**
	 * @return bool
	 */
	public static function isMinifyJsEnabled()
	{
		if (defined('WPACU_IS_MINIFY_JS_ENABLED')) {
			return WPACU_IS_MINIFY_JS_ENABLED;
		}

		// Request Minify On The Fly
		// It will preview the page with JS minified
		// Only if the admin is logged-in as it uses more resources (CPU / Memory)
		if (array_key_exists('wpacu_js_minify', $_GET) && Menu::userCanManageAssets()) {
			self::isMinifyJsEnabledChecked(true);
			return true;
		}

		if ( array_key_exists('wpacu_no_js_minify', $_GET) || // not on query string request (debugging purposes)
		     is_admin() || // not for Dashboard view
		     (! Main::instance()->settings['minify_loaded_js']) || // Minify JS has to be Enabled
		     (Main::instance()->settings['test_mode'] && ! Menu::userCanManageAssets()) ) { // Does not trigger if "Test Mode" is Enabled
			self::isMinifyJsEnabledChecked(false);
			return false;
		}

		if (defined('WPACU_CURRENT_PAGE_ID') && WPACU_CURRENT_PAGE_ID > 0 && is_singular()) {
			// If "Do not minify JS on this page" is checked in "Asset CleanUp: Options" side meta box
			$pageOptions = MetaBoxes::getPageOptions( WPACU_CURRENT_PAGE_ID );

			if ( isset( $pageOptions['no_js_minify'] ) && $pageOptions['no_js_minify'] ) {
				self::isMinifyJsEnabledChecked(false);
				return false;
			}
		}

		if (OptimizeJs::isOptimizeJsEnabledByOtherParty('if_enabled')) {
			self::isMinifyJsEnabledChecked(false);
			return false;
		}

		return true;
	}

	/**
	 * @param $value
	 */
	public static function isMinifyJsEnabledChecked($value)
	{
		if (! defined('WPACU_IS_MINIFY_JS_ENABLED')) {
			define('WPACU_IS_MINIFY_JS_ENABLED', $value);
		}
	}
}
