<?php
defined('ABSPATH') OR exit;

class Mcafeesecure
{
    public static function activate()
    {
        update_option('mcafeesecure_active', 1);
    }

    public static function scripts($hook){
        if ('plugins.php' == $hook) {

            wp_enqueue_script('mcafeesecure-bootstrap-js', plugins_url('/js/bootstrap.min.js', WP_PLUGIN_DIR . '/mcafee-secure/mcafee-secure.php'), array('jquery'));
            $mcafeesecure_ajax_object = array('ajax_url' => admin_url('admin-ajax.php'), 'data' => get_option('mcafeesecure_data', "{}"));

        }else if (strpos($hook, "mcafee-secure-settings") !== false) {
            wp_enqueue_style('mcafeesecure-settings-css', plugins_url('../css/settings.css',__FILE__));
            wp_enqueue_script('mcafeesecure-mcafeesecure-js', plugins_url('../js/mcafeesecure.js',__FILE__));
        }

    }

    public static function get_site_id(){
        $existing_site_id = get_option('mcafeesecure_site_id');
        if (!empty($existing_site_id)) {
            return $existing_site_id;
        }

        $endpoint_host = "https://www.trustedsite.com";
        $arrHost = parse_url(home_url('', $scheme = 'http'));
        $host = $arrHost['host'];

        $sitemap_req_url = $endpoint_host . "/rpc/ajax?do=lookup-site-status&host=" . urlencode($host);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $sitemap_req_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        
        $rjson = json_decode($response, true);
        $site_id = $rjson['siteId'];
        update_option('mcafeesecure_site_id', $site_id);
        return $site_id;
    }

    public static function install()
    {
        add_shortcode('mcafeesecure', 'Mcafeesecure::engagement_trustmark_shortcode');
        add_shortcode('mcafeesecure_sip', 'Mcafeesecure::sip_trustmark_shortcode');
        add_action('admin_menu', 'Mcafeesecure::admin_menus');
        add_action('wp_footer', 'Mcafeesecure::inject_code');
        add_action('do_robots', 'Mcafeesecure::robots');

        add_action('admin_enqueue_scripts', 'Mcafeesecure::scripts');
        add_action('wp_ajax_mcafeesecure_get_data', 'Mcafeesecure::ajax_get_data');
        add_action('wp_ajax_mcafeesecure_save_data', 'Mcafeesecure::ajax_save_data');

        add_filter('plugin_action_links_mcafee-secure/mcafee-secure.php', 'Mcafeesecure::add_plugin_settings_link');

        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            Mcafeesecure::install_woocommerce();
        }
    }

    public static function robots(){
        $site_id = Mcafeesecure::get_site_id();
        if(!empty($site_id)){
            echo "\nSitemap: http://cdn.ywxi.net/sitemap/".$site_id."/1.xml\n";
        }else{
            error_log("Failed to retrieve site ID");
        }
    }

    public static function request_sip($order_id)
    {
        $endpoint_host = "https://www.trustedsite.com";

        $order = wc_get_order($order_id);
        $order_total = sprintf((int)(wc_format_decimal($order->get_total(),2)*100));
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $order_number = $order->get_order_number();
        $email = $order->get_billing_email();
        $country_code = $order->get_billing_country();
        $state_code = $order->get_billing_state();

        if (empty($email)) {
            return;
        }

        $arrHost = parse_url(home_url('', $scheme = 'http'));
        $host = $arrHost['host'];

        $sip_req_url = $endpoint_host . "/rpc/ajax?do=track-site-conversion&jsoncallback=f&t=purchase&s=6&o=" . urlencode($order_number) . "&e=" . urlencode($email) . "&fn=" . urlencode($first_name) . "&ln=" . urlencode($last_name) . "&c=" . urlencode($country_code) . "&st=" . urlencode($state_code) . "&h=" . urlencode($host) . "&a=" . urlencode($order_total);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $sip_req_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public static function inject_sip_modal($order_id) {

        $email = wc_get_order($order_id)->get_billing_email();
        echo <<<EOT
            <script type="text/javascript">
              (function() {
                var sipScript = document.createElement('script');
                 sipScript.setAttribute("class","mcafeesecure-track-conversion");
                 sipScript.setAttribute("type","text/javascript");
                 sipScript.setAttribute("data-type","purchase");
                 sipScript.setAttribute("data-orderid", $order_id);
                 sipScript.setAttribute("data-email", "$email");
                 sipScript.setAttribute("src", "https://cdn.ywxi.net/js/conversion.js");
                 document.getElementsByTagName("head")[0].appendChild(sipScript);
              })();
            </script>
EOT;
    }

    public static function install_woocommerce()
    {
        add_action('woocommerce_thankyou', 'Mcafeesecure::request_sip');
        add_action('woocommerce_thankyou', 'Mcafeesecure::inject_sip_modal');
    }

    public static function ajax_get_data()
    {
        wp_send_json(get_option('mcafeesecure_data', array("empty" => 1)));
    }

    public static function ajax_save_data()
    {
        update_option('mcafeesecure_data', $_POST['data']);
        wp_send_json(get_option('mcafeesecure_data', array("empty" => 1)));
    }

    public static function deactivate()
    {
        delete_option("mcafeesecure_active");
    }

    public static function uninstall()
    {
        delete_option("mcafeesecure_active");
        delete_option("mcafeesecure_data");
        delete_option("mcafeesecure_site_id");
    }

    public static function engagement_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        $width = min(max(60, $width), 120);
        return "<script src='https://cdn.ywxi.net/js/inline.js?w=" . $width . "'></script>";
    }

    public static function sip_trustmark_shortcode($atts = array())
    {
        return "<script src='https://cdn.ywxi.net/js/inline.js?t=103'></script>"; }

    public static function admin_menus()
    {

        add_options_page(
            'McAfee SECURE', 
            'McAfee SECURE', 
            'activate_plugins', 
            'mcafee-secure-settings', 
            'Mcafeesecure::settings_page');

    }

    public static function add_plugin_settings_link( $links )
    {
        array_unshift( $links, '<a href="options-general.php?page=mcafee-secure-settings">Settings</a>' );
        return $links;
    }

    public static function settings_page()
    {
        require WP_PLUGIN_DIR . '/mcafee-secure/lib/settings_page.php';
    }

    public static function inject_code()
    {
        echo <<<EOT
            <script type="text/javascript">
              (function() {
                var sa = document.createElement('script'); sa.type = 'text/javascript'; sa.async = true;
                sa.src = ('https:' == document.location.protocol ? 'https://cdn' : 'http://cdn') + '.ywxi.net/js/1.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sa, s);
              })();
            </script>
EOT;
    }
}

?>