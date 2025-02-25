<?php
/*
 * Plugin Name: Asset CleanUp: Page Speed Booster
 * Plugin URI: https://wordpress.org/plugins/wp-asset-clean-up/
 * Version: 1.3.4.3
 * Description: Unload Chosen Scripts & Styles from Posts/Pages to reduce HTTP Requests, Combine/Minify CSS/JS files
 * Author: Gabriel Livan
 * Author URI: http://gabelivan.com/
 * Text Domain: wp-asset-clean-up
 * Domain Path: /languages
*/

// Is the Pro version triggered before the Lite one and are both plugins active?
if (! defined('WPACU_PLUGIN_VERSION')) {
	define('WPACU_PLUGIN_VERSION', '1.3.4.3');
}

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__.'/early-triggers.php';

if (assetCleanUpNoLoad()) {
	return; // do not continue
}

// Premium plugin version already exists, is it active?
// Do not load the LITE version as it's pointless
// This action is valid starting from LITE version 1.2.6.8
// From 1.0.3, the PRO version works independently (does not need anymore LITE to be active and act as a parent plugin)

// If the pro version (version above 1.0.2) was triggered first, we'll just check one of its constants
// If the lite version was triggered first, then we'll check if the pro version is active
if (   defined('WPACU_PRO_NO_LITE_NEEDED') && WPACU_PRO_NO_LITE_NEEDED !== false
    && defined('WPACU_PRO_PLUGIN_VERSION') && WPACU_PRO_PLUGIN_VERSION !== false ) {
	return;
}

define('WPACU_PLUGIN_ID', 'wpassetcleanup'); // unique prefix
define('WPACU_PLUGIN_TITLE', 'Asset CleanUp'); // a short version of the plugin name
define('WPACU_PLUGIN_FILE', __FILE__);
define('WPACU_PLUGIN_BASE', plugin_basename(WPACU_PLUGIN_FILE));

define('WPACU_ADMIN_PAGE_ID_START', WPACU_PLUGIN_ID . '_getting_started');

// Do not load the plugin if the PHP version is below 5.4
// If PHP_VERSION_ID is not defined, then PHP version is below 5.2.7, thus the plugin is not usable
$wpacuWrongPhp = ((! defined('PHP_VERSION_ID')) || (defined('PHP_VERSION_ID') && PHP_VERSION_ID < 50400));

if ($wpacuWrongPhp && is_admin()) { // Dashboard
    add_action('admin_init',    'wpAssetCleanUpWrongPhp');
    add_action('admin_notices', 'wpAssetCleanUpWrongPhpNotice');

    /**
     * Deactivate the plugin because it has the wrong PHP version installed
     */
    function wpAssetCleanUpWrongPhp()
    {
        deactivate_plugins(WPACU_PLUGIN_BASE);

        // The premium extension too (if any)
        deactivate_plugins('wp-asset-clean-up-pro/wpacu-pro.php');
	    deactivate_plugins('wp-asset-clean-up-pro/wpacu.php');
    }

    /**
     * Print the message to the user after the plugin was deactivated
     */
    function wpAssetCleanUpWrongPhpNotice()
    {
	    echo '<div class="error is-dismissible"><p>'.

	         sprintf(
		         __('%1$s requires %2$s PHP version installed. You have %3$s.', 'wp-asset-clean-up'),
		         '<strong>'.WPACU_PLUGIN_TITLE.'</strong>',
		         '<span style="color: green;"><strong>5.4+</strong></span>',
		         '<strong>'.PHP_VERSION.'</strong>'
	         ) . ' '.
	         __('If your website is compatible with PHP 7+ (e.g. you can check with your developers or contact the hosting company), it\'s strongly recommended to upgrade for a better performance.', 'wp-asset-clean-up').' '.
	         __('The plugin has been deactivated.', 'wp-asset-clean-up').

	         '</p></div>';

        if (array_key_exists('active', $_GET)) {
            unset($_GET['activate']);
        }
    }
} elseif ($wpacuWrongPhp) { // Front
    return;
}

define('WPACU_PLUGIN_DIR',          __DIR__);
define('WPACU_PLUGIN_CLASSES_PATH', WPACU_PLUGIN_DIR.'/classes/');
define('WPACU_PLUGIN_URL',          plugins_url('', WPACU_PLUGIN_FILE));

// Upgrade to Pro Sales Page
define('WPACU_PLUGIN_GO_PRO_URL',   'https://gabelivan.com/items/wp-asset-cleanup-pro/');

// Global Values
define('WPACU_LOAD_ASSETS_REQ_KEY', WPACU_PLUGIN_ID . '_load');

require_once WPACU_PLUGIN_DIR.'/wpacu-load.php';

if (! is_admin()) {
	require_once WPACU_PLUGIN_DIR . '/vendor/autoload.php';
}

// [wpacu_lite]
$wpacuSettingsList = $wpacuSettings->getAll();

if (! $wpacuSettingsList['disable_freemius']) {
	require_once WPACU_PLUGIN_DIR . '/freemius-load.php';
}
// [/wpacu_lite]

