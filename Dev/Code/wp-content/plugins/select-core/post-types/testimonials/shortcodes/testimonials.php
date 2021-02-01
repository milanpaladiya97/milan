<?php

namespace QodeCore\CPT\Testimonials\Shortcodes;


use QodeCore\Lib;

/**
 * Class Testimonials
 * @package QodeCore\CPT\Testimonials\Shortcodes
 */
class Testimonials implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'qodef_testimonials';

        add_action('vc_before_init', array($this, 'vcMap'));
    }

    /**
     * Returns base for shortcode
     * @return string
     */
    public function getBase() {
        return $this->base;
    }

    /**
     * Maps shortcode to Visual Composer
     *
     * @see vc_map()
     */
    public function vcMap() {
        if(function_exists('vc_map')) {
            vc_map( array(
                'name' => 'Testimonials',
                'base' => $this->base,
                'category' => 'by SELECT',
                'icon' => 'icon-wpb-testimonials extended-custom-icon',
                'allowed_container_element' => 'vc_row',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => 'Layout',
                        'param_name' => 'layout',
                        'value' => array(
                            'Cards Carousel' => 'cards_carousel',
                            'Standard Carousel' => 'standard_carousel'
                        ),
                        'save_always' => true,
                        'description' => ''
                    ),
                    array(
                        'type' => 'textfield',
						'admin_label' => true,
                        'heading' => 'Category',
                        'param_name' => 'category',
                        'value' => '',
                        'description' => 'Category Slug (leave empty for all)'
                    ),
                    array(
                        'type' => 'textfield',
                        'admin_label' => true,
                        'heading' => 'Number',
                        'param_name' => 'number',
                        'value' => '',
                        'description' => 'Number of Testimonials'
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => 'Type',
                        'param_name' => 'type',
                        'value' => array(
                            'Transparent' => 'transparent',
                            'Filled' => 'filled',
                            'Dark' => 'dark'
                        ),
                        'save_always' => true,
                        'description' => '',
                        'dependency' => array('element' => 'layout', 'value' => array('cards_carousel'))
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => 'Show Title',
                        'param_name' => 'show_title',
                        'value' => array(
                            'Yes' => 'yes',
                            'No' => 'no'
                        ),
						'save_always' => true,
                        'description' => ''
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => 'Show Author',
                        'param_name' => 'show_author',
                        'value' => array(
                            'Yes' => 'yes',
                            'No' => 'no'
                        ),
						'save_always' => true,
                        'description' => ''
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => 'Show Author Job Position',
                        'param_name' => 'show_position',
                        'value' => array(
                            'Yes' => 'yes',
							'No' => 'no',
                        ),
						'save_always' => true,
                        'dependency' => array('element' => 'show_author', 'value' => array('yes')),
                        'description' => ''
                    ), 
                    array(
                        'type' => 'textfield',
                        'admin_label' => true,
                        'heading' => 'Animation speed',
                        'param_name' => 'animation_speed',
                        'value' => '',
                        'description' => __('Speed of slide animation in miliseconds','select-core')
                    ),
                    array(
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'heading' => 'Show Navigation Arrows',
                        'param_name' => 'show_navigation_arrows',
                        'value' => array(
                            'Yes' => 'yes',
                            'No' => 'no'
                        ),
                        'save_always' => true,
                        'description' => '',
                        'dependency' => array('element' => 'layout', 'value' => array('cards_carousel'))
                    )
                )
            ) );
        }
    }

    /**
     * Renders shortcodes HTML
     *
     * @param $atts array of shortcode params
     * @param $content string shortcode content
     * @return string
     */
    public function render($atts, $content = null) {
        
        $args = array(
            'layout' => 'cards_carousel',
            'number' => '-1',
            'category' => '',
            'type' => 'transparent',
            'show_title' => 'yes',
            'show_author' => 'yes',
            'show_position' => 'yes',
            'animation_speed' => '',
            'show_navigation_arrows' => 'yes'
        );
		$params = shortcode_atts($args, $atts);
		
		//Extract params for use in method
		extract($params);
		
		$data_attr = $this->getDataParams($params);
		$query_args = $this->getQueryParams($params);

        $testimonial_classes = array();
        $testimonial_classes[] = $params['type'];
        $testimonial_classes[] = $params['layout'];
        $testimonial_classes =  implode(' ', $testimonial_classes);

		$html = '';
        $html .= '<div class="qodef-testimonials-holder qodef-grid-section clearfix">';
        if($layout == 'cards_carousel') {
            $html .= '<div class="qodef-testimonials qodef-section-inner ' . $testimonial_classes . '" ' . $data_attr . '>';
        }
        else if($layout == 'standard_carousel') {
            $html .= '<div class="qodef-testimonials ' . $testimonial_classes . '" ' . $data_attr . '>';
        }
        $i = 0;
        $opened = false;
        query_posts($query_args);
        if (have_posts()) :
            while (have_posts()) : the_post();
                $i++;
                $author = get_post_meta(get_the_ID(), 'qodef_testimonial_author', true);
                $text = get_post_meta(get_the_ID(), 'qodef_testimonial_text', true);
                $title = get_post_meta(get_the_ID(), 'qodef_testimonial_title', true);
                $job = get_post_meta(get_the_ID(), 'qodef_testimonial_author_position', true);

				$params['author'] = $author;
				$params['text'] = $text;
				$params['title'] = $title;
				$params['job'] = $job;
				$params['current_id'] = get_the_ID();
                if($layout == 'cards_carousel') {
                    if($i%3 == 1){
                        $html .= '<div class="qodef-testimonials-slider-item">';
                        $opened = true;
                    }

                    $html .= qode_core_get_shortcode_module_template_part('testimonials','testimonials-template', 'cards', $params);

                    if($i%3 == 0) {
                        $html .= '</div>';
                        $opened = false;
                    }
                }
                else if($layout == 'standard_carousel') {
                    $html .= '<div class="qodef-testimonials-slider-item">';
                    $html .= qode_core_get_shortcode_module_template_part('testimonials','testimonials-template', 'standard', $params);
                    $html .= '</div>';
                }

            endwhile;
        else:
            $html .= __('Sorry, no posts matched your criteria.', 'select-core');
        endif;

        wp_reset_query();
        if($opened && $layout == 'cards_carousel') {
            $html .= '</div>';
        }
        $html .= '</div>';
        if($layout == 'cards_carousel' && $show_navigation_arrows == 'yes') {
            $html .= '<div class="owl-buttons">';
            $html .= '<div class="owl-prev"><span class="qodef-prev-icon"><i class="fa fa-chevron-left"></i></span></div>';
            $html .= '<div class="owl-next"><span class="qodef-next-icon"><i class="fa fa-chevron-right"></i></span></div>';
        }
        $html .= '</div>';
		$html .= '</div>';
		
        return $html;
    }
	/**
    * Generates testimonial data attribute array
    *
    * @param $params
    *
    * @return string
    */
	private function getDataParams($params){
		$data_attr = '';
		
		if(!empty($params['animation_speed'])){
			$data_attr .= ' data-animation-speed ="' . $params['animation_speed'] . '"';
		}

        if(!empty($params['layout'])){
            $data_attr .= ' data-layout ="' . $params['layout'] . '"';
        }
		
		return $data_attr;
	}
	/**
    * Generates testimonials query attribute array
    *
    * @param $params
    *
    * @return array
    */
	private function getQueryParams($params){
		
		$args = array(
            'post_type' => 'testimonials',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => $params['number']
        );

        if ($params['category'] != '') {
            $args['testimonials_category'] = $params['category'];
        }
		return $args;
	}
	 
}