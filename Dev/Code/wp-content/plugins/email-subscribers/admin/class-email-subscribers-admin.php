<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      4.0
 *
 * @package    Email_Subscribers
 * @subpackage Email_Subscribers/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Email_Subscribers
 * @subpackage Email_Subscribers/admin
 * @author     Your Name <email@example.com>
 */
class Email_Subscribers_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    4.0
	 * @access   private
	 * @var      string $email_subscribers The ID of this plugin.
	 */
	private $email_subscribers;

	/**
	 * The version of this plugin.
	 *
	 * @since    4.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $email_subscribers The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    4.0
	 *
	 */
	public function __construct( $email_subscribers, $version ) {

		$this->email_subscribers = $email_subscribers;
		$this->version           = $version;

		// Reorder ES Submenu
		add_filter( 'custom_menu_order', array( $this, 'submenu_order' ) );


		add_action( 'admin_menu', array( $this, 'email_subscribers_admin_menu' ) );
		add_action( 'wp_ajax_es_klawoo_subscribe', array( $this, 'klawoo_subscribe' ) );
		add_action( 'admin_footer', array( $this, 'remove_submenu' ) );
		add_action( 'edit_form_advanced', array( $this, 'add_spam_score_utm_link' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    4.0
	 */
	public function enqueue_styles() {
		$screen             = get_current_screen();
		$screen_id          = $screen ? $screen->id : '';
		$enqueue_on_screens = array(
			'toplevel_page_es_dashboard',
			'email-subscribers_page_es_subscribers',
			'email-subscribers_page_es_lists',
			'email-subscribers_page_es_forms',
			'email-subscribers_page_es_campaigns',
			'email-subscribers_page_es_newsletters',
			'email-subscribers_page_es_notifications',
			'edit-es_template',
			'email-subscribers_page_es_reports',
			'email-subscribers_page_es_tools',
			'email-subscribers_page_es_settings',
			'email-subscribers_page_es_general_information',
			'email-subscribers_page_es_pricing',
		);
		//all admin notice
		if ( ! in_array( $screen_id, $enqueue_on_screens, true ) ) {
			return;
		}
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Email_Subscribers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Email_Subscribers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->email_subscribers, plugin_dir_url( __FILE__ ) . 'css/email-subscribers-admin.css', array(), $this->version, 'all' );


		$get_page = Email_Subscribers::get_request( 'page' );

		if ( ! empty( $get_page ) && 'es_settings' === $get_page ) {
			// wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'email-jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    4.0
	 */
	public function enqueue_scripts() {
		$screen             = get_current_screen();
		$screen_id          = $screen ? $screen->id : '';
		$enqueue_on_screens = array(
			'toplevel_page_es_dashboard',
			'email-subscribers_page_es_subscribers',
			'email-subscribers_page_es_lists',
			'email-subscribers_page_es_forms',
			'email-subscribers_page_es_campaigns',
			'email-subscribers_page_es_newsletters',
			'email-subscribers_page_es_notifications',
			'edit-es_template',
			'email-subscribers_page_es_reports',
			'email-subscribers_page_es_tools',
			'email-subscribers_page_es_settings',
			'email-subscribers_page_es_general_information',
			'email-subscribers_page_es_pricing',
		);
		//all admin notice
		if ( ! in_array( $screen_id, $enqueue_on_screens, true ) ) {
			return;
		}
		wp_enqueue_script( $this->email_subscribers, plugin_dir_url( __FILE__ ) . 'js/email-subscribers-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' ), $this->version, false );
	}

	public function remove_submenu() {
		//remove submenues
		?>
        <script type="text/javascript">
			jQuery(document).ready(function () {
				var removeSubmenu = ['ig-es-broadcast', 'ig-es-lists', 'ig-es-post-notifications'];
				jQuery.each(removeSubmenu, function (key, id) {
					jQuery("#" + id).parent('a').parent('li').hide();
				});
			})
        </script>
		<?php
	}

	public function email_subscribers_admin_menu() {
		// This adds the main menu page
		add_menu_page( __( 'Email Subscribers', 'email-subscribers' ), __( 'Email Subscribers', 'email-subscribers' ), 'edit_posts', 'es_dashboard', array( $this, 'es_dashboard_callback' ), 'dashicons-email', 30 );

		// Submenu
		add_submenu_page( 'es_dashboard', __( 'Dashboard', 'email-subscribers' ), __( 'Dashboard', 'email-subscribers' ), 'edit_posts', 'es_dashboard', array( $this, 'es_dashboard_callback' ) );
		add_submenu_page( 'es_dashboard', __( 'Lists', 'email-subscribers' ), '<span id="ig-es-lists">' . __( 'Lists', 'email-subscribers' ) . '</span>', 'edit_posts', 'es_lists', array( $this, 'load_lists' ) );
		add_submenu_page( 'es_dashboard', __( 'Post Notifications', 'email-subscribers' ), '<span id="ig-es-post-notifications">' . __( 'Post Notifications', 'email-subscribers' ) . '</span>', 'edit_posts', 'es_notifications', array( $this, 'load_post_notifications' ) );
		add_submenu_page( 'es_dashboard', __( 'Broadcast', 'email-subscribers' ), '<span id="ig-es-broadcast">' . __( 'Broadcast', 'email-subscribers' ) . '</span>', 'edit_posts', 'es_newsletters', array( $this, 'load_newsletters' ) );
		add_submenu_page( 'es_dashboard', __( 'Reports', 'email-subscribers' ), __( 'Reports', 'email-subscribers' ), 'edit_posts', 'es_reports', array( $this, 'load_reports' ) );
		add_submenu_page( 'es_dashboard', __( 'Audience', 'email-subscribers' ), __( 'Audience', 'email-subscribers' ), 'edit_posts', 'es_subscribers', array( $this, 'load_audience' ) );
		add_submenu_page( 'es_dashboard', __( 'Campaigns', 'email-subscribers' ), __( 'Campaigns', 'email-subscribers' ), 'edit_posts', 'es_campaigns', array( $this, 'load_campaigns' ) );
		add_submenu_page( 'es_dashboard', __( 'Settings', 'email-subscribers' ), __( 'Settings', 'email-subscribers' ), 'edit_posts', 'es_settings', array( $this, 'load_settings' ) );
		add_submenu_page( 'es_dashboard', __( 'Forms', 'email-subscribers' ), __( 'Forms', 'email-subscribers' ), 'edit_posts', 'es_forms', array( $this, 'load_forms' ) );
		add_submenu_page( null, __( 'Template Preview', 'email-subscribers' ), __( 'Template Preview', 'email-subscribers' ), 'edit_posts', 'es_template_preview', array( $this, 'load_preview' ) );
	}

	public function plugins_loaded() {
		ES_Templates_Table::get_instance();
		new Export_Subscribers();
		new ES_Handle_Post_Notification();
		ES_Handle_Sync_Wp_User::get_instance();
		new ES_Import_Subscribers();
		ES_Info::get_instance();
		ES_Newsletters::get_instance();
		ES_Tools::get_instance();
	}

	// Function for Klawoo's Subscribe form on Help & Info page
	public static function klawoo_subscribe() {
		$url = 'http://app.klawoo.com/subscribe';

		if ( ! empty( $_POST ) ) {
			$params = $_POST;
		} else {
			exit();
		}
		$method = 'POST';
		$qs     = http_build_query( $params );

		$options = array(
			'timeout' => 15,
			'method'  => $method
		);

		if ( $method == 'POST' ) {
			$options['body'] = $qs;
		} else {
			if ( strpos( $url, '?' ) !== false ) {
				$url .= '&' . $qs;
			} else {
				$url .= '?' . $qs;
			}
		}

		$response = wp_remote_request( $url, $options );

		if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
			$data = $response['body'];
			if ( $data != 'error' ) {

				$message_start = substr( $data, strpos( $data, '<body>' ) + 6 );
				$remove        = substr( $message_start, strpos( $message_start, '</body>' ) );
				$message       = trim( str_replace( $remove, '', $message_start ) );
				echo( $message );
				exit();
			}
		}
		exit();
	}

	public function load_lists() {
		$list = ES_Lists_Table::get_instance();
		$list->es_lists_callback();
	}

	public function load_post_notifications() {
		$post_notifications = ES_Post_Notifications_Table::get_instance();
		$post_notifications->es_notifications_callback();
	}

	public function load_newsletters() {
		$newsletters = ES_Newsletters::get_instance();
		$newsletters->es_newsletters_settings_callback();
	}

	public function load_reports() {
		$reports = ES_Reports_Table::get_instance();
		$reports->es_reports_callback();
	}

	public function load_audience() {
		$contacts = ES_Subscribers_Table::get_instance();
		$contacts->plugin_settings_page();
	}

	public function load_campaigns() {
		$campaigns = ES_Campaigns_Table::get_instance();
		$campaigns->es_campaigns_callback();
	}


	public function load_settings() {
		$settings = ES_Admin_Settings::get_instance();
		$settings->es_settings_callback();
	}

	public function load_forms() {
		$forms = ES_Forms_Table::get_instance();
		$forms->es_forms_callback();
	}

	public function load_preview() {
		$preview = ES_Templates_Table::get_instance();
		$preview->es_template_preview_callback();
	}

	public function do_send( $data ) {

		$to_email       = $data['to_email'];
		$subject        = $data['subject'];
		$email_template = $data['email_template'];
		$headers        = $data['headers'];

		wp_mail( $to_email, $subject, $email_template, $headers );
	}

	function submenu_order( $menu_order ) {
		global $submenu;

		$es_menus = isset( $submenu['es_dashboard'] ) ? $submenu['es_dashboard'] : array();

		if ( ! empty( $es_menus ) ) {

			$es_menu_order = array(
				'es_dashboard',
				'es_subscribers',
				'es_lists',
				'es_forms',
				'es_campaigns',
				'edit.php?post_type=es_template',
				'es_notifications',
				'es_newsletters',
				'es_reports',
				'es_tools',
				'es_settings',
				'es_general_information',
				'es_pricing'
			);

			$order = array_flip( $es_menu_order );

			$reorder_es_menu = array();
			foreach ( $es_menus as $menu ) {
				$reorder_es_menu[ $order[ $menu[2] ] ] = $menu;
			}

			ksort( $reorder_es_menu );

			$submenu['es_dashboard'] = $reorder_es_menu;

		}

		# Return the new submenu order
		return $menu_order;
	}

	public function es_dashboard_callback() {
		$es_plugin_data     = get_plugin_data( plugin_dir_path( __DIR__ ) . 'email-subscribers.php' );
		$es_current_version = $es_plugin_data['Version'];
		$admin_email        = get_option( 'admin_email' );

		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dashboard.php';

	}

	public static function es_feedback() {
		// if ( get_option( 'es_star_review' ) != 1 ) {
		// 	echo '<div class="notice notice-warning" style="background-color: #FFF;"><p style="letter-spacing: 0.6px;">If you like <strong>Email Subscribers</strong>, please consider leaving us a <a target="_blank" href="https://wordpress.org/support/plugin/email-subscribers/reviews/?filter=5#new-post"><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span></a> rating. A huge thank you from Icegram in advance! <a style="float:right" class="es-admin-btn es-admin-btn-secondary" href="' . admin_url() . 'admin.php?page=es_dashboard&dismiss_admin_notice=1&option_name=es_star_review">No, I don\'t like it</a></p></div>';

		// }
	}


	public static function add_spam_score_utm_link() {
		global $post, $pagenow;
		if ( $post->post_type !== 'es_template' ) {
			return;
		}
		?>
        <script>
			jQuery('#submitdiv').after('<div class="es_upsale"><a style="text-decoration:none;" target="_blank" href="https://www.icegram.com/documentation/how-ready-made-template-in-in-email-subscribers-look/?utm_source=in_app&utm_medium=es_template&utm_campaign=es_upsale"><img title="Get readymade templates" style="width:100%;border:0.3em #d46307 solid" src="<?php echo EMAIL_SUBSCRIBERS_URL?>/admin/images/starter-tmpl.png"/><p style="background: #d46307; color: #FFF; padding: 4px; width: 100%; text-align:center">Get readymade beautiful email templates</p></a></div>');
        </script>
		<?php
	}

}
