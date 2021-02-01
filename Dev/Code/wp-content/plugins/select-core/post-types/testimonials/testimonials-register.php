<?php
namespace QodeCore\CPT\Testimonials;

use QodeCore\Lib;


/**
 * Class TestimonialsRegister
 * @package QodeCore\CPT\Testimonials
 */
class TestimonialsRegister implements Lib\PostTypeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'testimonials';
        $this->taxBase = 'testimonials_category';
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
     * Regsiters custom post type with WordPress
     */
    private function registerPostType() {
        global $qode_startit_Framework;

        $menuPosition = 5;
        $menuIcon = 'dashicons-admin-post';

        if(qode_core_theme_installed()) {
            $menuPosition = $qode_startit_Framework->getSkin()->getMenuItemPosition('testimonial');
            $menuIcon = $qode_startit_Framework->getSkin()->getMenuIcon('testimonial');
        }

        register_post_type('testimonials',
            array(
                'labels' 		=> array(
                    'name' 				=> __('Testimonials','select-core' ),
                    'singular_name' 	=> __('Testimonial','select-core' ),
                    'add_item'			=> __('New Testimonial','select-core'),
                    'add_new_item' 		=> __('Add New Testimonial','select-core'),
                    'edit_item' 		=> __('Edit Testimonial','select-core')
                ),
                'public'		=>	false,
                'show_in_menu'	=>	true,
                'rewrite' 		=> 	array('slug' => 'testimonials'),
                'menu_position' => 	$menuPosition,
                'show_ui'		=>	true,
                'has_archive'	=>	false,
                'hierarchical'	=>	false,
                'supports'		=>	array('title', 'thumbnail'),
                'menu_icon'  =>  $menuIcon
            )
        );
    }

    /**
     * Registers custom taxonomy with WordPress
     */
    private function registerTax() {
        $labels = array(
            'name' => __( 'Testimonials Categories', 'select-core' ),
            'singular_name' => __( 'Testimonial Category', 'select-core' ),
            'search_items' =>  __( 'Search Testimonials Categories','select-core' ),
            'all_items' => __( 'All Testimonials Categories','select-core' ),
            'parent_item' => __( 'Parent Testimonial Category','select-core' ),
            'parent_item_colon' => __( 'Parent Testimonial Category:','select-core' ),
            'edit_item' => __( 'Edit Testimonials Category','select-core' ),
            'update_item' => __( 'Update Testimonials Category','select-core' ),
            'add_new_item' => __( 'Add New Testimonials Category','select-core' ),
            'new_item_name' => __( 'New Testimonials Category Name','select-core' ),
            'menu_name' => __( 'Testimonials Categories','select-core' ),
        );

        register_taxonomy($this->taxBase, array($this->base), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'show_admin_column' => true,
            'rewrite' => array( 'slug' => 'testimonials-category' ),
        ));
    }

}