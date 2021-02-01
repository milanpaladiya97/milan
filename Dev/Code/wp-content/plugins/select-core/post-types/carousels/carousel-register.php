<?php
namespace QodeCore\CPT\Carousels;

use QodeCore\Lib;

/**
 * Class CarouselRegister
 * @package QodeCore\CPT\Carousels
 */
class CarouselRegister implements Lib\PostTypeInterface {
    /**
     * @var string
     */
    private $base;
    /**
     * @var string
     */
    private $taxBase;

    public function __construct() {
        $this->base = 'carousels';
        $this->taxBase = 'carousels_category';
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
            $menuPosition = $qode_startit_Framework->getSkin()->getMenuItemPosition('carousel');
            $menuIcon = $qode_startit_Framework->getSkin()->getMenuIcon('carousel');
        }

        register_post_type($this->base,
            array(
                'labels'    => array(
                    'name'        => __('Select Carousel','select-core' ),
                    'menu_name' => __('Select Carousel','select-core' ),
                    'all_items' => __('Carousel Items','select-core' ),
                    'add_new' =>  __('Add New Carousel Item','select-core'),
                    'singular_name'   => __('Carousel Item','select-core' ),
                    'add_item'      => __('New Carousel Item','select-core'),
                    'add_new_item'    => __('Add New Carousel Item','select-core'),
                    'edit_item'     => __('Edit Carousel Item','select-core')
                ),
                'public'    =>  false,
                'show_in_menu'  =>  true,
                'rewrite'     =>  array('slug' => 'carousels'),
                'menu_position' =>  $menuPosition,
                'show_ui'   =>  true,
                'has_archive' =>  false,
                'hierarchical'  =>  false,
                'supports'    =>  array('title'),
                'menu_icon'  =>  $menuIcon
            )
        );
    }

    /**
     * Registers custom taxonomy with WordPress
     */
    private function registerTax() {
        $labels = array(
            'name' => __( 'Carousels', 'select-core' ),
            'singular_name' => __( 'Carousel', 'select-core' ),
            'search_items' =>  __( 'Search Carousels','select-core' ),
            'all_items' => __( 'All Carousels','select-core' ),
            'parent_item' => __( 'Parent Carousel','select-core' ),
            'parent_item_colon' => __( 'Parent Carousel:','select-core' ),
            'edit_item' => __( 'Edit Carousel','select-core' ),
            'update_item' => __( 'Update Carousel','select-core' ),
            'add_new_item' => __( 'Add New Carousel','select-core' ),
            'new_item_name' => __( 'New Carousel Name','select-core' ),
            'menu_name' => __( 'Carousels','select-core' ),
        );

        register_taxonomy($this->taxBase, array($this->base), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'show_admin_column' => true,
            'rewrite' => array( 'slug' => 'carousels-category' ),
        ));
    }

}