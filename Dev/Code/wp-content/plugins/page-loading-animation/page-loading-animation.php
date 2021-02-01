<?php

/*
Plugin Name: Preloader Animation
Author: Mehedi Hasan Kanon
Author URI: http://mhkanon.com
Description: Preloader Animation plugin is very helpful for you if you find an animation add before loading your website. Here you can add animation easily before loading page. You can customize the animation and background color.
Text Domain: page_loading_animation
Domain Path: /languages
Version: 1.1.1
Stable tag: 1.1.1
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/



/*============================================================
					Page Loading Animation
==============================================================*/


require_once plugin_dir_path( __FILE__ ) .'pla_menu.php';
require_once plugin_dir_path( __FILE__ ) .'pla_fields.php';


class pla_reg {

	public function __construct() {
		add_action( 'wp_head', array( $this,'pla_main' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'pla_script' ) );
	}

	public function pla_script() {
		wp_enqueue_style( 'pla_style2', plugins_url( 'assets/css/pla_style.css', __FILE__ ) );
		wp_enqueue_script('pla_scripts1', plugins_url('assets/js/modernizr-2.6.2.min.js', __FILE__ ) ,array('jquery'), false );
		wp_enqueue_script('pla_scripts2', plugins_url('assets/js/pla_jquery.js', __FILE__ ) ,array('jquery'), false );
	}

	public function pla_main() { 	
		
		$pla_loader = get_option('pla_animation_choose');

		if($pla_loader == '') {
			$pla_loader = 'def2';
		}else {
			$pla_loader;
		}


		if($pla_loader == 'no') {
			echo '<div id="pla_no"></div>';
		}

		if($pla_loader == 'def1') {
			echo '<div id="pla_loader1"></div>';
		}

		if($pla_loader == 'def2') {
			echo '<div id="pla_loader_wrapper">
					 <div id="pla_loader"></div>

					 <div class="pla_loader_section pla_section_left"></div>
	            	 <div class="pla_loader_section pla_section_right"></div>
				 </div>';
		}

		if($pla_loader == 'def3') {
			echo '<div id="pla_loader3"></div>';
		}

		if($pla_loader == 'def4') {
			echo '<div id="pla_loader4"></div>';
		}

		if($pla_loader == 'def5') {
			echo '<div id="pla_spinner"></div>';
		}

		if($pla_loader == 'def6') {
			echo '<div id="pla_spinner2">
					<div class="double-bounce1"></div>
					<div class="double-bounce2"></div>
				</div>';
		}

		if($pla_loader == 'def7') {
			echo '<div id="pla_spinner3">
					<div class="rect1"></div>
					<div class="rect2"></div>
					<div class="rect3"></div>
					<div class="rect4"></div>
					<div class="rect5"></div>
				 </div>';
		}

		if($pla_loader == 'def8') {
			echo '<div id="pla_spinner4"></div>';
		}

		if($pla_loader == 'def9') {
			echo '<div id="pla_spinner5">
					<div class="bounce1"></div>
					<div class="bounce2"></div>
					<div class="bounce3"></div>
				 </div>';
		}

		if($pla_loader == 'def10') {
			echo '<div id="pla-sk-cube-grid">
					<div class="sk-cube sk-cube1"></div>
					<div class="sk-cube sk-cube2"></div>
					<div class="sk-cube sk-cube3"></div>
					<div class="sk-cube sk-cube4"></div>
					<div class="sk-cube sk-cube5"></div>
					<div class="sk-cube sk-cube6"></div>
					<div class="sk-cube sk-cube7"></div>
					<div class="sk-cube sk-cube8"></div>
					<div class="sk-cube sk-cube9"></div>
				 </div>';
		}

		if($pla_loader == 'def11') {
			echo '<div class="page-loader11">
				  <div>
				    <div class="page-loader-body11">
				      <div class="cssload-loader11">
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				        <div class="cssload-side"></div>
				      </div>
				    </div>
				   </div>
				 </div>';
		}

		if($pla_loader == 'custom') {
			echo '<div id="pla_loader_custom"></div>'; ?>

			<style>

				#pla_loader_custom {
				    position: fixed;
				    left: 0px;
				    top: 0px;
				    width: 100%;
				    height: 100%;
				    z-index: 9999;
				    background: url('<?php echo get_option("pla_animation_upload") ?>') 50% 50% no-repeat <?php echo get_option("pla_animation_bg_color") ?>;
				}

			</style>

			<?php
		}

	}

}

new pla_reg();