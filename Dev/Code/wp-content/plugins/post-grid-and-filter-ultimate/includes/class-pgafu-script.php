<?php
/**
 * Script Class
 *
 * Handles the script and style functionality of plugin
 *
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Pgafu_Script {
	
	function __construct() {
		
		// Action to add style at front side
		add_action( 'wp_enqueue_scripts', array($this, 'pgafu_plugin_style') );

		// Action to add script at front side
		add_action( 'wp_enqueue_scripts', array($this, 'pgafu_plugin_script') );		
		
	}

	/**
	 * Function to add script at front side
	 * 
	 * @package Post grid and filter ultimate
	 * @since 1.0.0
	 */
	function pgafu_plugin_style(){		
		
		// Registring and enqueing public css
		wp_register_style( 'pgafu-public-style', PGAFU_URL.'assets/css/pgafu-public.css', array(), PGAFU_VERSION );
		wp_enqueue_style( 'pgafu-public-style');
	}

	/**
	 * Function to add script at front side
	 * 
	 * @package Post grid and filter ultimate
	 * @since 1.0.0
	 */
	function pgafu_plugin_script() {		
		
		// Registring tooltip js
		if( !wp_script_is( 'wpos-filterizr-js', 'registered' ) ) {
			wp_register_script( 'wpos-filterizr-js', PGAFU_URL.'assets/js/filterizr.js', array('jquery'), PGAFU_VERSION, true );
		}
		
		// Registring public js
		wp_register_script( 'pgafu-public-js', PGAFU_URL.'assets/js/pgafu-public.js', array('jquery'), PGAFU_VERSION, true );	
		
	}	
}

$pgafu_script = new Pgafu_Script();