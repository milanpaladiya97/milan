<?php
namespace QodeCore\CPT\Carousels\Shortcodes;

use QodeCore\Lib;

/**
 * Class Carousel
 * @package QodeCore\CPT\Carousels\Shortcodes
 */
class Carousel implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'qodef_carousel';

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
     * Maps shortcode to Visual Composer. Hooked on vc_before_init
     *
     * @see qode_core_get_carousel_slider_array_vc()
     */
    public function vcMap() {

        vc_map( array(
            'name' => 'Select Carousel',
            'base' => $this->base,
            'category' => 'by SELECT',
            'icon' => 'icon-wpb-carousel-slider extended-custom-icon',
            'allowed_container_element' => 'vc_row',
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => 'Carousel Slider',
                    'param_name' => 'carousel',
                    'value' => qode_core_get_carousel_slider_array_vc(),
                    'description' => '',
                    'admin_label' => true
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => 'Order By',
                    'param_name' => 'orderby',
                    'value' => array(
                        '' => '',
                        'Title' => 'title',
                        'Date' => 'date'
                    ),
                    'description' => ''
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => 'Order',
                    'param_name' => 'order',
                    'value' => array(
                        '' => '',
                        'ASC' => 'ASC',
                        'DESC' => 'DESC',
                    ),
                    'description' => ''
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => 'Number of items showing',
                    'param_name' => 'number_of_items',
                    'value' => array(
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                        '6' => '6'
                    ),
                    'save_always' => true,
                    'admin_label' => true,
                    'description' => ''
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => 'Image Animation',
                    'param_name' => 'image_animation',
                    'value' => array(
                        'Image Change' => 'image-change',
                        'Image Zoom' => 'image-zoom'
                    ),
                    'admin_label' => true,
                    'save_always' => true,
                    'description' => 'Note: Only on "Image Change" zoom image will be used'
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => 'Show navigation?',
                    'param_name' => 'show_navigation',
                    'value' => array(
                        'Yes' => 'yes',
                        'No' => 'no',
                    ),
                    'save_always' => true,
                    'admin_label' => true,
                    'description' => ''
                )
            )
        ) );

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
            'carousel' => '',
            'orderby' => 'date',
            'order' => 'ASC',
            'number_of_items' => '5',
            'image_animation' => 'image-change',
            'show_navigation' => ''
        );

        $params = shortcode_atts($args, $atts);
		$params['carousel_data_attributes'] = $this->getCarouselDataAttributes($params);

		extract($params);

        $html = '';
        $carousel_class = '';

        if($show_navigation != '' && $show_navigation == 'yes') {
            $carousel_class = 'with_navigation';
        }

        if ($carousel !== '') {

            $html .= '<div class="qodef-carousel-holder ' . esc_attr($carousel_class) .' clearfix">';
            $html .= '<div class="qodef-carousel" ' .  qode_startit_get_inline_attrs($carousel_data_attributes) . '>';

            $args = array(
                'post_type' => 'carousels',
                'carousels_category' => $params['carousel'],
                'orderby' => $params['orderby'],
                'order' => $params['order'],
                'posts_per_page' => '-1'
            );

            $query = new \WP_Query($args);

            if ($query->have_posts()) {
                while($query->have_posts()) {
                    $query->the_post();
                    $carousel_item = $this->getCarouselItemData(get_the_ID(), $params);
                    $html .= qode_core_get_shortcode_module_template_part('carousels', 'carousel-template', '', $carousel_item);
                }
            }

            wp_reset_postdata();


            $html .= '</div>';
            $html .= '</div>';

        }

        return $html;
    }

    /**
     * Return all data that carousel needs, images, titles, links, etc
     *
     * @param $params
     * @return array
     */
    private function getCarouselItemData($item_id, $params) {

        $carousel_item = array();

        if (get_post_meta($item_id, 'qodef_carousel_image', true) !== '') {
            $carousel_item['image'] = get_post_meta($item_id, 'qodef_carousel_image', true);
        } else {
            $carousel_item['image'] = '';
        }

        if ($params['image_animation'] == 'image-change' && get_post_meta($item_id, 'qodef_carousel_hover_image', true) !== '') {
            $carousel_item['hover_image'] = get_post_meta($item_id, 'qodef_carousel_hover_image', true);
            $carousel_item['hover_class'] = 'qodef-has-hover-image';
        } else {
            $carousel_item['hover_image'] = '';
            $carousel_item['hover_class'] = '';
        }

        if (get_post_meta($item_id, 'qodef_carousel_item_link', true) != '') {
            $carousel_item['link'] = get_post_meta($item_id, 'qodef_carousel_item_link', true);
        } else {
            $carousel_item['link'] = '';
        }

        if (get_post_meta($item_id, 'qodef_carousel_item_target', true) != '') {
            $carousel_item['target'] = get_post_meta($item_id, 'qodef_carousel_item_target', true);
        } else {
            $carousel_item['target'] = '_self';
        }

        $carousel_item['title'] = get_the_title();

        $carousel_item['carousel_image_classes'] = $this->getCarouselImageClasses($params);
        $carousel_item['hover_type'] = $this->getCarouselHoverType($params);

        return $carousel_item;

    }

	/**
	 * Return CSS classes for carousel image
	 *
	 * @param $params
	 * @return array
	 */
	private function getCarouselImageClasses($params) {

		$carousel_image_classes = array();
		if($params['image_animation'] !== '') {
			$carousel_image_classes[] = 'qodef-' . $params['image_animation'];
		}

		return implode(' ', $carousel_image_classes);

	}

    /**
     * Return CSS classes for carousel image
     *
     * @param $params
     * @return array
     */
    private function getCarouselHoverType($params) {

        $type = '';
        if($params['image_animation'] !== '') {
            $type = $params['image_animation'];
        }

        return $type;

    }

	/**
	 * Return data attributes for carousel
	 *
	 * @param $params
	 * @return array
	 */
	private function getCarouselDataAttributes($params) {

		$carousel_data = array();

		if ($params['number_of_items'] !== '') {
			$carousel_data['data-items'] = $params['number_of_items'];
		}
		if ($params['show_navigation'] !== '') {
			$carousel_data['data-navigation'] = $params['show_navigation'];
		}

		return $carousel_data;

	}

}