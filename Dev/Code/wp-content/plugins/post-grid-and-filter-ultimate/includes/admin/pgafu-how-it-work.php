<?php
/**
 * Pro Designs and Plugins Feed
 *
 * @package Post grid and filter ultimate
 * @since 1.1
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Action to add menu
add_action('admin_menu', 'pgafu_register_design_page');

/**
 * Register plugin design page in admin menu
 * 
 * @package Album and Image Gallery Plus Lightbox
 * @since 1.0.0
 */
function pgafu_register_design_page() {
 	add_menu_page( __('Post Grid And Filter', 'post-grid-and-filter-ultimate'), __('Post Grid And Filter', 'post-grid-and-filter-ultimate'), 'manage_options', 'pgafu-about',  'pgafu_designs_page', 'dashicons-sticky', 6 );
}

/**
 * Function to display plugin design HTML
 * 
 * @package Post grid and filter ultimate
 * @since 1.1
 */
function pgafu_designs_page() {

	$wpos_feed_tabs = pgafu_help_tabs();
	$active_tab 	= isset($_GET['tab']) ? $_GET['tab'] : 'how-it-work';
?>
		
	<div class="wrap pgafu-wrap">

		<h2 class="nav-tab-wrapper">
			<?php
			foreach ($wpos_feed_tabs as $tab_key => $tab_val) {
				$tab_name	= $tab_val['name'];
				$active_cls = ($tab_key == $active_tab) ? 'nav-tab-active' : '';
				$tab_link 	= add_query_arg( array('page' => 'pgafu-about', 'tab' => $tab_key), admin_url('admin.php') );
			?>

			<a class="nav-tab <?php echo $active_cls; ?>" href="<?php echo $tab_link; ?>"><?php echo $tab_name; ?></a>

			<?php } ?>
		</h2>
		
		<div class="pgafu-tab-cnt-wrp">
		<?php
			if( isset($active_tab) && $active_tab == 'how-it-work' ) {
				pgafu_howitwork_page();
			}
			else if( isset($active_tab) && $active_tab == 'plugins-feed' ) {
				echo pgafu_get_plugin_design( 'plugins-feed' );
			} else {
				echo pgafu_get_plugin_design( 'offers-feed' );
			}
		?>
		</div><!-- end .pgafu-tab-cnt-wrp -->

	</div><!-- end .pgafu-wrap -->

<?php
}

/**
 * Gets the plugin design part feed
 *
 * @package Post grid and filter ultimate
 * @since 1.1
 */
function pgafu_get_plugin_design( $feed_type = '' ) {
	
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
	
	// If tab is not set then return
	if( empty($active_tab) ) {
		return false;
	}

	// Taking some variables
	$wpos_feed_tabs = pgafu_help_tabs();
	$transient_key 	= isset($wpos_feed_tabs[$active_tab]['transient_key']) 	? $wpos_feed_tabs[$active_tab]['transient_key'] 	: 'pgafu_' . $active_tab;
	$url 			= isset($wpos_feed_tabs[$active_tab]['url']) 			? $wpos_feed_tabs[$active_tab]['url'] 				: '';
	$transient_time = isset($wpos_feed_tabs[$active_tab]['transient_time']) ? $wpos_feed_tabs[$active_tab]['transient_time'] 	: 172800;
	$cache 			= get_transient( $transient_key );
	
	if ( false === $cache ) {
		
		$feed 			= wp_remote_get( esc_url_raw( $url ), array( 'timeout' => 120, 'sslverify' => false ) );
		$response_code 	= wp_remote_retrieve_response_code( $feed );
		
		if ( ! is_wp_error( $feed ) && $response_code == 200 ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( $transient_key, $cache, $transient_time );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the data from the server. Please try again later.', 'post-grid-and-filter-ultimate' ) . '</div>';
		}
	}
	return $cache;	
}

/**
 * Function to get plugin feed tabs
 *
 * @package Post grid and filter ultimate
 * @since 1.1
 */
function pgafu_help_tabs() {
	$wpos_feed_tabs = array(
						'how-it-work' 	=> array(
													'name' => __('How It Works', 'post-grid-and-filter-ultimate'),
												),
						'plugins-feed' 	=> array(
													'name' 				=> __('Our Plugins', 'post-grid-and-filter-ultimate'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/plugins-data.php',
													'transient_key'		=> 'wpos_plugins_feed',
													'transient_time'	=> 172800
												),
						'offers-feed' 	=> array(
													'name'				=> __('WPOS Offers', 'post-grid-and-filter-ultimate'),
													'url'				=> 'http://wponlinesupport.com/plugin-data-api/wpos-offers.php',
													'transient_key'		=> 'wpos_offers_feed',
													'transient_time'	=> 86400,
												)
					);
	return $wpos_feed_tabs;
}

/**
 * Function to get 'How It Works' HTML
 *
 * @package Post grid and filter ultimate
 * @since 1.1
 */
function pgafu_howitwork_page() { ?>
	
	<style type="text/css">
		.wpos-pro-box .hndle{background-color:#0073AA; color:#fff;}
		.wpos-pro-box .postbox{background:#dbf0fa none repeat scroll 0 0; border:1px solid #0073aa; color:#191e23;}
		.postbox-container .wpos-list li:before{font-family: dashicons; content: "\f139"; font-size:20px; color: #0073aa; vertical-align: middle;}
		.pgafu-wrap .wpos-button-full{display:block; text-align:center; box-shadow:none; border-radius:0;}
		.pgafu-shortcode-preview{background-color: #e7e7e7; font-weight: bold; padding: 2px 5px; display: inline-block; margin:0 0 2px 0;}
	</style>

	<div class="post-box-container">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			
				<!--How it workd HTML -->
				<div id="post-body-content">
					<div class="metabox-holder">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								
								<h3 class="hndle">
									<span><?php _e( 'How It Works - Display and shortcode', 'post-grid-and-filter-ultimate' ); ?></span>
								</h3>
								
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr>
												<th>
													<label><?php _e('Geeting Started with Post Slider', 'post-grid-and-filter-ultimate'); ?>:</label>
												</th>
												<td>
													<ul>
														<li><?php _e('Step-1. This plugin create a tab under "Post grid and filter ultimate – How It Works".', 'post-grid-and-filter-ultimate'); ?></li>
														<li><?php _e('Step-2. This plugin get all the POST from WordPress post section with a simple shortcode', 'post-grid-and-filter-ultimate'); ?></li>
													</ul>
												</td>
											</tr>

											<tr>
												<th>
													<label><?php _e('How Shortcode Works', 'post-grid-and-filter-ultimate'); ?>:</label>
												</th>
												<td>
													<ul>
														<li><?php _e('Step-1. Create a page like Latet Post OR add the shortcode in a page.', 'post-grid-and-filter-ultimate'); ?></li>
														<li><?php _e('Step-2. Put below shortcode as per your need.', 'post-grid-and-filter-ultimate'); ?></li>
													</ul>
												</td>
											</tr>

											<tr>
												<th>
													<label><?php _e('All Shortcodes', 'post-grid-and-filter-ultimate'); ?>:</label>
												</th>
												<td>
													<span class="pgafu-shortcode-preview">[pgaf_post_grid]</span> – <?php _e('Post Grid Shortcode', 'post-grid-and-filter-ultimate'); ?><br>
													<span class="pgafu-shortcode-preview">[pgaf_post_filter]</span> – <?php _e('Post grid Shortcode. Where you can use 4 designs', 'post-grid-and-filter-ultimate'); ?>
												</td>
											</tr>						
												
											<tr>
												<th>
													<label><?php _e('Need Support?', 'post-grid-and-filter-ultimate'); ?></label>
												</th>
												<td>
													<p><?php _e('Check plugin document for shortcode parameters and demo for designs.', 'post-grid-and-filter-ultimate'); ?></p> <br/>
													<a class="button button-primary" href="https://www.wponlinesupport.com/plugins-documentation/post-grid-and-filter-ultimate/?utm_source=hp&event=doc" target="_blank"><?php _e('Documentation', 'post-grid-and-filter-ultimate'); ?></a>									
													<a class="button button-primary" href="http://demo.wponlinesupport.com/post-grid-and-filter-ultimate-demo/?utm_source=hp&event=demo" target="_blank"><?php _e('Demo for Designs', 'post-grid-and-filter-ultimate'); ?></a>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->
				</div><!-- #post-body-content -->
				
				<!--Upgrad to Pro HTML -->
				<div id="postbox-container-1" class="postbox-container">

					<!-- Help to improve this plugin! -->
					<div class="metabox-holder">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
									<h3 class="hndle">
										<span><?php _e( 'Help to improve this plugin!', 'post-grid-and-filter-ultimate' ); ?></span>
									</h3>									
									<div class="inside">										
										<p>Enjoyed this plugin? You can help by rate this plugin <a href="https://wordpress.org/support/plugin/post-grid-and-filter-ultimate/reviews/?filter=5" target="_blank">5 stars!</a></p>
									</div><!-- .inside -->
							</div><!-- #general -->
						</div><!-- .meta-box-sortables ui-sortable -->
					</div><!-- .metabox-holder -->
				</div><!-- #post-container-1 -->

			</div><!-- #post-body -->
		</div><!-- #poststuff -->
	</div><!-- #post-box-container -->
<?php }