<?php
if (!function_exists ('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
class QodeStartitImport {

    public $message = "";
    public $attachments = false;
    function __construct() {
        add_action('admin_menu', array(&$this, 'qode_admin_import'));
        add_action('admin_init', array(&$this, 'qode_register_theme_settings'));

    }
    function qode_register_theme_settings() {
        register_setting( 'qode_options_import_page', 'qode_options_import');
    }

    function init_qode_import() {
        if(isset($_REQUEST['import_option'])) {
            $import_option = $_REQUEST['import_option'];
            if($import_option == 'content'){
                $this->import_content('proya_content.xml');
            }elseif($import_option == 'custom_sidebars') {
                $this->import_custom_sidebars('custom_sidebars.txt');
            } elseif($import_option == 'widgets') {
                $this->import_widgets('widgets.txt','custom_sidebars.txt');
            } elseif($import_option == 'options'){
                $this->import_options('options.txt');
            }elseif($import_option == 'menus'){
                $this->import_menus('menus.txt');
            }elseif($import_option == 'settingpages'){
                $this->import_settings_pages('settingpages.txt');
            }elseif($import_option == 'complete_content'){
                $this->import_content('proya_content.xml');
                $this->import_options('options.txt');
                $this->import_widgets('widgets.txt','custom_sidebars.txt');
                $this->import_menus('menus.txt');
                $this->import_settings_pages('settingpages.txt');
                $this->message = esc_html__("Content imported successfully", 'select-core');
            }
        }
    }

    public function import_content($file){
        ob_start();
        require_once(QODE_CORE_ABS_PATH . '/import/class.wordpress-importer.php');
        $qode_import = new WP_Import();
        set_time_limit(0);

        $qode_import->fetch_attachments = $this->attachments;
        $returned_value = $qode_import->import($file);
        if(is_wp_error($returned_value)){
            $this->message = esc_html__("An Error Occurred During Import", "select-core");
        }
        else {
            $this->message = esc_html__("Content imported successfully", "select-core");
        }
        ob_get_clean();
    }

    public function import_widgets($file, $file2){
        $this->import_custom_sidebars($file2);
        $options = $this->file_options($file);
        foreach ((array) $options['widgets'] as $qode_widget_id => $qode_widget_data) {
            update_option( 'widget_' . $qode_widget_id, $qode_widget_data );
        }
        $this->import_sidebars_widgets($file);
        $this->message = esc_html__("Widgets imported successfully", 'select-core');
    }

    public function import_sidebars_widgets($file){
        $qode_sidebars = get_option("sidebars_widgets");
        unset($qode_sidebars['array_version']);
        $data = $this->file_options($file);
        if ( is_array($data['sidebars']) ) {
            $qode_sidebars = array_merge( (array) $qode_sidebars, (array) $data['sidebars'] );
            unset($qode_sidebars['wp_inactive_widgets']);
            $qode_sidebars = array_merge(array('wp_inactive_widgets' => array()), $qode_sidebars);
            $qode_sidebars['array_version'] = 2;
            wp_set_sidebars_widgets($qode_sidebars);
        }
    }

    public function import_custom_sidebars($file){
        $options = $this->file_options($file);
        update_option( 'qode_sidebars', $options);
        $this->message = esc_html__("Custom sidebars imported successfully", 'select-core');
    }

    public function import_options($file){
        $options = $this->file_options($file);
        update_option( 'qode_options_startit', $options);
        $this->message = esc_html__("Options imported successfully", 'select-core');
    }

    public function import_menus($file){
        global $wpdb;
        $qode_terms_table = $wpdb->prefix . "terms";
        $this->menus_data = $this->file_options($file);
        $menu_array = array();
        foreach ($this->menus_data as $registered_menu => $menu_slug) {
            $term_rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $qode_terms_table where slug=%s", $menu_slug), ARRAY_A);
            if(isset($term_rows[0]['term_id'])) {
                $term_id_by_slug = $term_rows[0]['term_id'];
            } else {
                $term_id_by_slug = null;
            }
            $menu_array[$registered_menu] = $term_id_by_slug;
        }
        set_theme_mod('nav_menu_locations', array_map('absint', $menu_array ) );

    }
    public function import_settings_pages($file){
        $pages = $this->file_options($file);
        foreach($pages as $qode_page_option => $qode_page_id){
            update_option( $qode_page_option, $qode_page_id);
        }
    }

    public function file_options($file){
        $file_content = "";
        $file_for_import = get_template_directory() . '/includes/import/files/' . $file;
        /*if ( file_exists($file_for_import) ) {
            $file_content = $this->qode_file_contents($file_for_import);
        } else {
            $this->message = esc_html__("File doesn't exist", "startit");
        }*/
        $file_content = $this->qode_file_contents($file);
        if ($file_content) {
            $unserialized_content = unserialize(base64_decode($file_content));
            if ($unserialized_content) {
                return $unserialized_content;
            }
        }
        return false;
    }

    function qode_file_contents( $path ) {
        $url      = "http://export.select-themes.com/".$path;
		$response = wp_remote_get($url);
		$body     = wp_remote_retrieve_body($response);
		return $body;
    }

    function qode_admin_import() {
        if(qode_core_theme_installed()) {
	        global $qode_startit_Framework;
	
			$slug = "_tabimport";
			$this->pagehook = add_submenu_page(
				'qode_startit_theme_menu',
				'Select Options - Qode Import',                   // The value used to populate the browser's title bar when the menu page is active
				'Import',                   // The text of the menu in the administrator's sidebar
				'administrator',                  // What roles are able to access the menu
				'qode_startit_theme_menu'.$slug,                // The ID used to bind submenu items to this menu
				array($qode_startit_Framework->getSkin(), 'renderImport')
			);
	
	        add_action('admin_print_scripts-'.$this->pagehook, 'qode_startit_enqueue_admin_scripts');
	        add_action('admin_print_styles-'.$this->pagehook, 'qode_startit_enqueue_admin_styles');
	        //$this->pagehook = add_menu_page('Qode Import', 'Qode Import', 'manage_options', 'qode_options_import_page', array(&$this, 'qode_generate_import_page'),'dashicons-download');
	    }
    }

}


function qode_init_import_object(){
	global $qode_startit_import_object;
	$qode_startit_import_object = new QodeStartitImport();;
}

add_action('init', 'qode_init_import_object');


if(!function_exists('qode_startit_dataImport')){
    function qode_startit_dataImport(){
        global $qode_startit_import_object;

        if ($_POST['import_attachments'] == 1)
            $qode_startit_import_object->attachments = true;
        else
            $qode_startit_import_object->attachments = false;

        $folder = "startit/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $qode_startit_import_object->import_content($folder.$_POST['xml']);

        die();
    }

    add_action('wp_ajax_qode_dataImport', 'qode_startit_dataImport');
}

if(!function_exists('qode_startit_widgetsImport')){
    function qode_startit_widgetsImport(){
        global $qode_startit_import_object;

        $folder = "startit/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $qode_startit_import_object->import_widgets($folder.'widgets.txt',$folder.'custom_sidebars.txt');

        die();
    }

    add_action('wp_ajax_qode_widgetsImport', 'qode_startit_widgetsImport');
}

if(!function_exists('qode_startit_optionsImport')){
    function qode_startit_optionsImport(){
        global $qode_startit_import_object;

        $folder = "startit/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $qode_startit_import_object->import_options($folder.'options.txt');

        die();
    }

    add_action('wp_ajax_qode_optionsImport', 'qode_startit_optionsImport');
}

if(!function_exists('qode_startit_otherImport')){
    function qode_startit_otherImport(){
        global $qode_startit_import_object;

        $folder = "startit/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $qode_startit_import_object->import_options($folder.'options.txt');
        $qode_startit_import_object->import_widgets($folder.'widgets.txt',$folder.'custom_sidebars.txt');
        $qode_startit_import_object->import_menus($folder.'menus.txt');
        $qode_startit_import_object->import_settings_pages($folder.'settingpages.txt');

        die();
    }

    add_action('wp_ajax_qode_otherImport', 'qode_startit_otherImport');
}