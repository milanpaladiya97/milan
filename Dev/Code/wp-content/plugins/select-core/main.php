<?php
/*
Plugin Name: Select Core
Description: Plugin that adds all post types needed by our theme
Author: Select Themes
Version: 1.3.1
*/

require_once 'load.php';

use QodeCore\CPT;
use QodeCore\Lib;

add_action('after_setup_theme', array(CPT\PostTypesRegister::getInstance(), 'register'));

Lib\ShortcodeLoader::getInstance()->load();

if(!function_exists('qode_core_activation')) {
    /**
     * Triggers when plugin is activated. It calls flush_rewrite_rules
     * and defines qode_startit  _core_on_activate action
     */
    function qode_core_activation() {
        do_action('qode_startit  _core_on_activate');

        QodeCore\CPT\PostTypesRegister::getInstance()->register();
        flush_rewrite_rules();
    }

    register_activation_hook(__FILE__, 'qode_core_activation');
}

if(!function_exists('qode_core_text_domain')) {
    /**
     * Loads plugin text domain so it can be used in translation
     */
    function qode_core_text_domain() {
        load_plugin_textdomain('select-core', false, QODE_CORE_REL_PATH.'/languages');
    }

    add_action('plugins_loaded', 'qode_core_text_domain');
}