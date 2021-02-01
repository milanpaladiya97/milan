<?php
/**
 * Plugin Name: Post grid and filter ultimate
 * Plugin URI: https://www.wponlinesupport.com/plugins/
 * Description: Easy to add and display post grid on your website with or without categoryies filter. Also work with Gutenberg shortcode block. 
 * Author: WP OnlineSupport
 * Author URI: https://www.wponlinesupport.com
 * Text Domain: post-grid-and-filter-ultimate
 * Domain Path: /languages/
 * Version: 1.1.3
 *
 * @package WordPress
 * @author WP OnlineSupport
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions
 * 
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */
if( !defined( 'PGAFU_VERSION' ) ) {
	define( 'PGAFU_VERSION', '1.1.3' ); // Version of plugin
}
if( !defined( 'PGAFU_DIR' ) ) {
	define( 'PGAFU_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'PGAFU_URL' ) ) {
	define( 'PGAFU_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'PGAFU_PLUGIN_BASENAME' ) ) {
    define( 'PGAFU_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}
if( !defined( 'PGAFU_POST_TYPE' ) ) {
	define( 'PGAFU_POST_TYPE', 'post' ); // Plugin post type name
}
if( !defined( 'PGAFU_CAT' ) ) {
	define( 'PGAFU_CAT', 'category' ); // Plugin taxonomy name
}

/**
 * Load Text Domain
 * This gets the plugin ready for translation
 * 
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */
add_action('plugins_loaded', 'pgafu_load_textdomain');
function pgafu_load_textdomain() {
	load_plugin_textdomain( 'post-grid-and-filter-ultimate', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

// Functions file
require_once( PGAFU_DIR . '/includes/pgafu-functions.php' );

// Script Class
require_once( PGAFU_DIR . '/includes/class-pgafu-script.php' );

// Shortcode File
require_once( PGAFU_DIR . '/includes/shortcode/pgafu-post-grid.php' );
require_once( PGAFU_DIR . '/includes/shortcode/pgafu-postgrid-filter.php' );

// Admin Class File
require_once( PGAFU_DIR . '/includes/admin/class-pgafu-admin.php' );

// How it work file, Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( PGAFU_DIR . '/includes/admin/pgafu-how-it-work.php' );
}


/* Plugin Analytics Data */
function wpos_analytics_anl67_load() {

    require_once dirname( __FILE__ ) . '/wpos-analytics/wpos-analytics.php';

    $wpos_analytics =  wpos_anylc_init_module( array(
                            'id'            => 67,
                            'file'          => plugin_basename( __FILE__ ),
                            'name'          => 'Post grid and filter ultimate',
                            'slug'          => 'post-grid-and-filter-ultimate',
                            'type'          => 'plugin',
                            'menu'          => 'pgafu-about',
                            'text_domain'   => 'post-grid-and-filter-ultimate',
                            'promotion'     => array( 
                                                    'bundle' => array(
                                                            'name'  => 'Download FREE 50+ Plugins, 10+ Themes and Dashboard Plugin',
                                                            'desc'  => 'Download FREE 50+ Plugins, 10+ Themes and Dashboard Plugin',
                                                            'file'  => 'https://www.wponlinesupport.com/latest/wpos-free-50-plugins-plus-12-themes.zip'
                                                        )
                                                    ),
                            'offers'        => array(
                                                    'trial_premium' => array(
                                                        'image' => 'http://analytics.wponlinesupport.com/?anylc_img=67',
                                                        'link'  => 'http://analytics.wponlinesupport.com/?anylc_redirect=67',
                                                        'desc'  => 'Or start using the plugin from admin menu',
                                                    )
                                                ),
                        ));

    return $wpos_analytics;
}

// Init Analytics
wpos_analytics_anl67_load();
/* Plugin Analytics Data Ends */