<?php
defined( 'ABSPATH' ) OR exit;

/**
 * ------------------------------------------------------------------------------------------------------------------
 * @package mcafeesecure
 * @version 1.8.0
 * Plugin Name: McAfee SECURE
 * Plugin URI: https://www.mcafeesecure.com/
 * Description: McAfee SECURE displays the trustmark on your website, increasing visitor confidence and conversion rates.
 * Author: TrustedSite
 * Version: 1.8.0
 * Author URI: https://www.mcafeesecure.com/trustedsite
 * ------------------------------------------------------------------------------------------------------------------
 */

if(defined('WP_INSTALLING') && WP_INSTALLING){
    return;
}
define('MCAFEESECURE_VERSION', '1.8.0');

add_action('activated_plugin','mcafeesecure_save_activation_error');
function mcafeesecure_save_activation_error(){
    update_option('mcafeesecure_plugin_error',  ob_get_contents());
}

require_once('lib/Mcafeesecure.php');
register_activation_hook(__FILE__, 'Mcafeesecure::activate');
register_deactivation_hook(__FILE__, 'Mcafeesecure::deactivate');
register_uninstall_hook(__FILE__, 'Mcafeesecure::uninstall');

Mcafeesecure::install();
