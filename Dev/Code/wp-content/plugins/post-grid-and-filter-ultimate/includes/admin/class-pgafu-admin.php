<?php
/**
 * Admin Class
 *
 * Handles the Admin side functionality of plugin
 *
 * @package Post grid and filter ultimate
 * @since 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class PGAFU_Admin {

	function __construct() {
		// Action to register admin menu
		add_action( 'admin_menu', array($this, 'pgafu_register_menu'), 12 );
	}

	
	/**
	 * Function to register admin menus
	 * 
	 * @package Post grid and filter ultimate
	 * @since 1.0.4
	 */
	function pgafu_register_menu() {

		// Register plugin premium page
		add_submenu_page( 'pgafu-about', __('Upgrade to PRO - Post grid and filter', 'post-grid-and-filter-ultimate'), '<span style="color:#2ECC71">'.__('Upgrade to PRO', 'post-grid-and-filter-ultimate').'</span>', 'manage_options', 'pgafu-premium', array($this, 'pgafu_premium_page') );
		
		// Register plugin hire us page
		add_submenu_page( 'pgafu-about', __('Hire Us', 'post-grid-and-filter-ultimate'), '<span style="color:#2ECC71">'.__('Hire Us', 'post-grid-and-filter-ultimate').'</span>', 'manage_options', 'pgafu-hireus', array($this, 'pgafu_hireus_page') );
		
	}

	/**
	 * Getting Started Page Html
	 * 
	 * @package Post grid and filter ultimate
	 * @since 1.0
	 */
	function pgafu_premium_page() {
		include_once( PGAFU_DIR . '/includes/admin/settings/premium.php' );
	}

	/**
	 * Getting Started Page Html
	 * 
	 * @package Post grid and filter ultimate
	 * @since 1.4
	 */
	function pgafu_hireus_page() {
		include_once( PGAFU_DIR . '/includes/admin/settings/hire-us.php' );
	}

	
}

$pgafu_admin = new PGAFU_Admin();