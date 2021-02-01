<?php
namespace QodeCore\CPT\Portfolio;

use QodeCore\Lib\PostTypeInterface;

/**
 * Class PortfolioRegister
 * @package QodeCore\CPT\Portfolio
 */
class PortfolioRegister implements PostTypeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'portfolio-item';
        $this->taxBase = 'portfolio-category';

        add_filter('single_template', array($this, 'registerSingleTemplate'));
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
        $this->registerTagTax();
    }

    /**
     * Registers portfolio single template if one does'nt exists in theme.
     * Hooked to single_template filter
     * @param $single string current template
     * @return string string changed template
     */
    public function registerSingleTemplate($single) {
        global $post;

        if($post->post_type == $this->base) {
            if(!file_exists(get_template_directory().'/single-portfolio-item.php')) {
                return QODE_CORE_CPT_PATH.'/portfolio/templates/single-'.$this->base.'.php';
            }
        }

        return $single;
    }

    /**
     * Registers custom post type with WordPress
     */
    private function registerPostType() {
        global $qode_startit_Framework, $qode_startit_options;

        $menuPosition = 5;
        $menuIcon = 'dashicons-admin-post';
        $slug = $this->base;

        if(qode_core_theme_installed()) {
            $menuPosition = $qode_startit_Framework->getSkin()->getMenuItemPosition('portfolio');
            $menuIcon = $qode_startit_Framework->getSkin()->getMenuIcon('portfolio');

            if(isset($qode_startit_options['portfolio_single_slug'])) {
                if($qode_startit_options['portfolio_single_slug'] != ""){
                    $slug = $qode_startit_options['portfolio_single_slug'];
                }
            }
        }

        register_post_type( $this->base,
            array(
                'labels' => array(
                    'name' => __( 'Portfolio','select-core' ),
                    'singular_name' => __( 'Portfolio Item','select-core' ),
                    'add_item' => __('New Portfolio Item','select-core'),
                    'add_new_item' => __('Add New Portfolio Item','select-core'),
                    'edit_item' => __('Edit Portfolio Item','select-core')
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => $slug),
                'menu_position' => $menuPosition,
                'show_ui' => true,
                'supports' => array('author', 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'comments'),
                'menu_icon'  =>  $menuIcon
            )
        );
    }

    /**
     * Registers custom taxonomy with WordPress
     */
    private function registerTax() {
        $labels = array(
            'name' => __( 'Portfolio Categories', 'select-core' ),
            'singular_name' => __( 'Portfolio Category', 'select-core' ),
            'search_items' =>  __( 'Search Portfolio Categories','select-core' ),
            'all_items' => __( 'All Portfolio Categories','select-core' ),
            'parent_item' => __( 'Parent Portfolio Category','select-core' ),
            'parent_item_colon' => __( 'Parent Portfolio Category:','select-core' ),
            'edit_item' => __( 'Edit Portfolio Category','select-core' ),
            'update_item' => __( 'Update Portfolio Category','select-core' ),
            'add_new_item' => __( 'Add New Portfolio Category','select-core' ),
            'new_item_name' => __( 'New Portfolio Category Name','select-core' ),
            'menu_name' => __( 'Portfolio Categories','select-core' ),
        );

        register_taxonomy($this->taxBase, array($this->base), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'portfolio-category' ),
        ));
    }

    /**
     * Registers custom tag taxonomy with WordPress
     */
    private function registerTagTax() {
        $labels = array(
            'name' => __( 'Portfolio Tags', 'select-core' ),
            'singular_name' => __( 'Portfolio Tag', 'select-core' ),
            'search_items' =>  __( 'Search Portfolio Tags','select-core' ),
            'all_items' => __( 'All Portfolio Tags','select-core' ),
            'parent_item' => __( 'Parent Portfolio Tag','select-core' ),
            'parent_item_colon' => __( 'Parent Portfolio Tags:','select-core' ),
            'edit_item' => __( 'Edit Portfolio Tag','select-core' ),
            'update_item' => __( 'Update Portfolio Tag','select-core' ),
            'add_new_item' => __( 'Add New Portfolio Tag','select-core' ),
            'new_item_name' => __( 'New Portfolio Tag Name','select-core' ),
            'menu_name' => __( 'Portfolio Tags','select-core' ),
        );

        register_taxonomy('portfolio-tag',array($this->base), array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'portfolio-tag' ),
        ));
    }
}