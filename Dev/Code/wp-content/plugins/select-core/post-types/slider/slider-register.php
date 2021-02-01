<?php
namespace QodeCore\CPT\Slider;

use QodeCore\Lib;

/**
 * Class SliderRegister
 * @package QodeCore\CPT\Slider
 */
class SliderRegister implements Lib\PostTypeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'slides';
        $this->taxBase = 'slides_category';
    }

    /**
     * @return string
     */
    public function getBase() {
        return $this->base;
    }

    /**
     * Registers custom post type with WordPress
     */
    public function register() {
        $this->registerPostType();
        $this->registerTax();
    }

    /**
     * Registers custom post type with WordPress
     */
    private function registerPostType() {
        global $qode_startit_Framework;

        $menuPosition = 5;
        $menuIcon = 'dashicons-admin-post';

        if(qode_core_theme_installed()) {
            $menuPosition = $qode_startit_Framework->getSkin()->getMenuItemPosition('slider');
            $menuIcon = $qode_startit_Framework->getSkin()->getMenuIcon('slider');
        }

        register_post_type($this->base,
            array(
                'labels' 		=> array(
                    'name' 				=> __('Select Slider','select-core' ),
                    'menu_name'	=> __('Select Slider','select-core' ),
                    'all_items'	=> __('Slides','select-core' ),
                    'add_new' =>  __('Add New Slide','select-core'),
                    'singular_name' 	=> __('Slide','select-core' ),
                    'add_item'			=> __('New Slide','select-core'),
                    'add_new_item' 		=> __('Add New Slide','select-core'),
                    'edit_item' 		=> __('Edit Slide','select-core')
                ),
                'public'		=>	false,
                'show_in_menu'	=>	true,
                'rewrite' 		=> 	array('slug' => 'slides'),
                'menu_position' => 	$menuPosition,
                'show_ui'		=>	true,
                'has_archive'	=>	false,
                'hierarchical'	=>	false,
                'supports'		=>	array('title', 'thumbnail', 'page-attributes'),
                'menu_icon'  =>  $menuIcon
            )
        );
    }

    /**
     * Registers custom taxonomy with WordPress
     */
    private function registerTax() {
        $labels = array(
            'name' => __( 'Sliders', 'select-core' ),
            'singular_name' => __( 'Slider', 'select-core' ),
            'search_items' =>  __( 'Search Sliders','select-core' ),
            'all_items' => __( 'All Sliders','select-core' ),
            'parent_item' => __( 'Parent Slider','select-core' ),
            'parent_item_colon' => __( 'Parent Slider:','select-core' ),
            'edit_item' => __( 'Edit Slider','select-core' ),
            'update_item' => __( 'Update Slider','select-core' ),
            'add_new_item' => __( 'Add New Slider','select-core' ),
            'new_item_name' => __( 'New Slider Name','select-core' ),
            'menu_name' => __( 'Sliders','select-core' ),
        );

        register_taxonomy($this->taxBase, array($this->base), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'show_admin_column' => true,
            'rewrite' => array( 'slug' => 'slides-category' ),
        ));
    }
}