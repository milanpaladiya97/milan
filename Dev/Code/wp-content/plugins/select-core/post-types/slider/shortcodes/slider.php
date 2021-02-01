<?php

namespace QodeCore\CPT\Slider\Shortcodes;

use QodeCore\Lib;

/**
 * Class Slider
 * @package QodeCore\CPT\Slider\Shortcodes
 */
class Slider implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'qodef_slider';
    }

    /**
     * Returns base for shortcode
     * @return string
     */
    public function getBase() {
        return $this->base;
    }

    /**
     * Renders shortcodes HTML
     *
     * @param $atts array of shortcode params
     * @param $content string shortcode content
     * @return string
     */
    public function render($atts, $content = null) {

        global $qode_startit_options;
        extract(shortcode_atts(array("slider" => "", "height" => "", "responsive_height" => "", "responsive_breakpoints" => "set1", "auto_start" => "", "anchor" => "", "animation_type" => "", "slide_animation_timeout" => "", "show_navigation_arrows" => "yes", "show_navigation_circles" => "yes"), $atts));
        $html = "";

        if ($slider != "") {
            $args = array(
                'post_type'       => 'slides',
                'slides_category' => $slider,
                'orderby'         => "menu_order",
                'order'           => "ASC",
                'posts_per_page'  => - 1
            );


            $slider_id = get_term_by('slug', $slider, 'slides_category')->term_id;
            $slider_meta = get_option("taxonomy_term_" . $slider_id);
            /* check if slider effects on header style - start */
            $slider_header_effect = $slider_meta['header_effect'];
            if ($slider_header_effect == 'yes') {
                $header_effect_class = 'qodef-header-effect';
            } else {
                $header_effect_class = '';
            }
            /* check if slider effects on header style - end */

            /* check if slider effects on navigation style - start */
            $slider_navigation_effect = $slider_meta['navigation_effect'];
            if ($slider_navigation_effect == 'yes') {
                $navigation_effect_class = 'qodef-navigation-effect';
            } else {
                $navigation_effect_class = '';
            }
            /* check if slider effects on navigation style - end */

            /* check if slider has parallax effect - start */
            $slider_css_position_class = '';
            $slider_parallax = 'yes';
            if (isset($slider_meta['slider_parallax_effect'])) {
                $slider_parallax = $slider_meta['slider_parallax_effect'];
            }
            if ($slider_parallax == 'no') {
                $data_parallax_effect = 'data-parallax="no"';
                $data_parallax_transform = '';
                $slider_css_position_class = 'qodef-relative-position';
            } else {
                $data_parallax_effect = 'data-parallax="yes"';
                $data_parallax_transform = 'data-start="transform: translateY(0px);" data-1440="transform: translateY(-500px);"';
            }

            /* check if slider has parallax effect - end */

            /* check if slider has prev/next thumb enabled - start */
            $slider_thumbs = 'no';
            if (isset($slider_meta['slider_thumbs'])) {
                $slider_thumbs = $slider_meta['slider_thumbs'];
            }
            if ($slider_thumbs == 'yes') {
                $slider_thumbs_class = 'qodef-slider-thumbs';
            } else {
                $slider_thumbs_class = '';
            }

            /* check if slider has prev/next thumb enabled - end */

            /* check if slider has prev/next numbers enabled - start */
            $slider_numbers = 'no';
            if($slider_meta['slider_numbers'] == 'yes') {
                $slider_numbers_class = 'qodef-slider-numbers';
                $slider_numbers = 'yes';
            } else {
                $slider_numbers_class = '';
            }
            /* check if slider has prev/next numbers enabled - end */

            /* set heights for slider - start */
            if ($height == "" || $height == "0") {
                $full_screen_class = "qodef-full-screen";
                $responsive_height_class = "";
                $height_class = "";
                $slide_holder_height = "";
                $slide_height = "";
                $data_height = "";
                $carouselinner_height = 'height: 100%';
            } else {
                $full_screen_class = "";
                $height_class = "qodef-has-height";
                if ($responsive_height == "yes") {
                    $responsive_height_class = "qodef-responsive-height";
                } else {
                    $responsive_height_class = "";
                }
                $slide_holder_height = "height: " . $height . "px;";
                $slide_height = "height: " . ($height) . "px;";
                $data_height = "data-height='" . $height . "'";
                $carouselinner_height = "height: " . ($height + 50) . "px;"; //because of the bottom gap on smooth scroll
            }
            /* set heights for slider - start */

            $anchor_data = '';
            if ($anchor != "") {
                $anchor_data .= 'data-qodef-anchor = "' . esc_attr($anchor) . '"';
            }

            /* set responsive breakpoints - start */
            $responsiveness_data = '';

            if ($height != "" && $responsive_height == "yes") {
                $responsiveness_data = 'data-qodef_responsive_breakpoints = "' . esc_attr($responsive_breakpoints) . '"';
            }

            if (isset($slider_meta['breakpoint1_graphic']) && $slider_meta['breakpoint1_graphic'] != '') {
                $breakpoint1_graphic = esc_attr($slider_meta['breakpoint1_graphic']);
            } else {
                $breakpoint1_graphic = 1;
            }
            if (isset($slider_meta['breakpoint2_graphic']) && $slider_meta['breakpoint2_graphic'] != '') {
                $breakpoint2_graphic = esc_attr($slider_meta['breakpoint2_graphic']);
            } else {
                $breakpoint2_graphic = 1;
            }
            if (isset($slider_meta['breakpoint3_graphic']) && $slider_meta['breakpoint3_graphic'] != '') {
                $breakpoint3_graphic = esc_attr($slider_meta['breakpoint3_graphic']);
            } else {
                $breakpoint3_graphic = 0.8;
            }
            if (isset($slider_meta['breakpoint4_graphic']) && $slider_meta['breakpoint4_graphic'] != '') {
                $breakpoint4_graphic = esc_attr($slider_meta['breakpoint4_graphic']);
            } else {
                $breakpoint4_graphic = 0.7;
            }
            if (isset($slider_meta['breakpoint5_graphic']) && $slider_meta['breakpoint5_graphic'] != '') {
                $breakpoint5_graphic = esc_attr($slider_meta['breakpoint5_graphic']);
            } else {
                $breakpoint5_graphic = 0.6;
            }
            if (isset($slider_meta['breakpoint6_graphic']) && $slider_meta['breakpoint6_graphic'] != '') {
                $breakpoint6_graphic = esc_attr($slider_meta['breakpoint6_graphic']);
            } else {
                $breakpoint6_graphic = 0.5;
            }
            if (isset($slider_meta['breakpoint7_graphic']) && $slider_meta['breakpoint7_graphic'] != '') {
                $breakpoint7_graphic = esc_attr($slider_meta['breakpoint7_graphic']);
            } else {
                $breakpoint7_graphic = 0.4;
            }

            if (isset($slider_meta['breakpoint1_title']) && $slider_meta['breakpoint1_title'] != '') {
                $breakpoint1_title = esc_attr($slider_meta['breakpoint1_title']);
            } else {
                $breakpoint1_title = 1;
            }
            if (isset($slider_meta['breakpoint2_title']) && $slider_meta['breakpoint2_title'] != '') {
                $breakpoint2_title = esc_attr($slider_meta['breakpoint2_title']);
            } else {
                $breakpoint2_title = 1;
            }
            if (isset($slider_meta['breakpoint3_title']) && $slider_meta['breakpoint3_title'] != '') {
                $breakpoint3_title = esc_attr($slider_meta['breakpoint3_title']);
            } else {
                $breakpoint3_title = 0.8;
            }
            if (isset($slider_meta['breakpoint4_title']) && $slider_meta['breakpoint4_title'] != '') {
                $breakpoint4_title = esc_attr($slider_meta['breakpoint4_title']);
            } else {
                $breakpoint4_title = 0.7;
            }
            if (isset($slider_meta['breakpoint5_title']) && $slider_meta['breakpoint5_title'] != '') {
                $breakpoint5_title = esc_attr($slider_meta['breakpoint5_title']);
            } else {
                $breakpoint5_title = 0.6;
            }
            if (isset($slider_meta['breakpoint6_title']) && $slider_meta['breakpoint6_title'] != '') {
                $breakpoint6_title = esc_attr($slider_meta['breakpoint6_title']);
            } else {
                $breakpoint6_title = 0.5;
            }
            if (isset($slider_meta['breakpoint7_title']) && $slider_meta['breakpoint7_title'] != '') {
                $breakpoint7_title = esc_attr($slider_meta['breakpoint7_title']);
            } else {
                $breakpoint7_title = 0.4;
            }

            if (isset($slider_meta['breakpoint1_subtitle']) && $slider_meta['breakpoint1_subtitle'] != '') {
                $breakpoint1_subtitle = esc_attr($slider_meta['breakpoint1_subtitle']);
            } else {
                $breakpoint1_subtitle = 1;
            }
            if (isset($slider_meta['breakpoint2_subtitle']) && $slider_meta['breakpoint2_subtitle'] != '') {
                $breakpoint2_subtitle = esc_attr($slider_meta['breakpoint2_subtitle']);
            } else {
                $breakpoint2_subtitle = 1;
            }
            if (isset($slider_meta['breakpoint3_subtitle']) && $slider_meta['breakpoint3_subtitle'] != '') {
                $breakpoint3_subtitle = esc_attr($slider_meta['breakpoint3_subtitle']);
            } else {
                $breakpoint3_subtitle = 0.8;
            }
            if (isset($slider_meta['breakpoint4_subtitle']) && $slider_meta['breakpoint4_subtitle'] != '') {
                $breakpoint4_subtitle = esc_attr($slider_meta['breakpoint4_subtitle']);
            } else {
                $breakpoint4_subtitle = 0.7;
            }
            if (isset($slider_meta['breakpoint5_subtitle']) && $slider_meta['breakpoint5_subtitle'] != '') {
                $breakpoint5_subtitle = esc_attr($slider_meta['breakpoint5_subtitle']);
            } else {
                $breakpoint5_subtitle = 0.6;
            }
            if (isset($slider_meta['breakpoint6_subtitle']) && $slider_meta['breakpoint6_subtitle'] != '') {
                $breakpoint6_subtitle = esc_attr($slider_meta['breakpoint6_subtitle']);
            } else {
                $breakpoint6_subtitle = 0.5;
            }
            if (isset($slider_meta['breakpoint7_subtitle']) && $slider_meta['breakpoint7_subtitle'] != '') {
                $breakpoint7_subtitle = esc_attr($slider_meta['breakpoint7_subtitle']);
            } else {
                $breakpoint7_subtitle = 0.4;
            }

            if (isset($slider_meta['breakpoint1_text']) && $slider_meta['breakpoint1_text'] != '') {
                $breakpoint1_text = esc_attr($slider_meta['breakpoint1_text']);
            } else {
                $breakpoint1_text = 1;
            }
            if (isset($slider_meta['breakpoint2_text']) && $slider_meta['breakpoint2_text'] != '') {
                $breakpoint2_text = esc_attr($slider_meta['breakpoint2_text']);
            } else {
                $breakpoint2_text = 1;
            }
            if (isset($slider_meta['breakpoint3_text']) && $slider_meta['breakpoint3_text'] != '') {
                $breakpoint3_text = esc_attr($slider_meta['breakpoint3_text']);
            } else {
                $breakpoint3_text = 0.8;
            }
            if (isset($slider_meta['breakpoint4_text']) && $slider_meta['breakpoint4_text'] != '') {
                $breakpoint4_text = esc_attr($slider_meta['breakpoint4_text']);
            } else {
                $breakpoint4_text = 0.7;
            }
            if (isset($slider_meta['breakpoint5_text']) && $slider_meta['breakpoint5_text'] != '') {
                $breakpoint5_text = esc_attr($slider_meta['breakpoint5_text']);
            } else {
                $breakpoint5_text = 0.6;
            }
            if (isset($slider_meta['breakpoint6_text']) && $slider_meta['breakpoint6_text'] != '') {
                $breakpoint6_text = esc_attr($slider_meta['breakpoint6_text']);
            } else {
                $breakpoint6_text = 0.5;
            }
            if (isset($slider_meta['breakpoint7_text']) && $slider_meta['breakpoint7_text'] != '') {
                $breakpoint7_text = esc_attr($slider_meta['breakpoint7_text']);
            } else {
                $breakpoint7_text = 0.4;
            }

            if (isset($slider_meta['breakpoint1_button']) && $slider_meta['breakpoint1_button'] != '') {
                $breakpoint1_button = esc_attr($slider_meta['breakpoint1_button']);
            } else {
                $breakpoint1_button = 1;
            }
            if (isset($slider_meta['breakpoint2_button']) && $slider_meta['breakpoint2_button'] != '') {
                $breakpoint2_button = esc_attr($slider_meta['breakpoint2_button']);
            } else {
                $breakpoint2_button = 1;
            }
            if (isset($slider_meta['breakpoint3_button']) && $slider_meta['breakpoint3_button'] != '') {
                $breakpoint3_button = esc_attr($slider_meta['breakpoint3_button']);
            } else {
                $breakpoint3_button = 0.8;
            }
            if (isset($slider_meta['breakpoint4_button']) && $slider_meta['breakpoint4_button'] != '') {
                $breakpoint4_button = esc_attr($slider_meta['breakpoint4_button']);
            } else {
                $breakpoint4_button = 0.7;
            }
            if (isset($slider_meta['breakpoint5_button']) && $slider_meta['breakpoint5_button'] != '') {
                $breakpoint5_button = esc_attr($slider_meta['breakpoint5_button']);
            } else {
                $breakpoint5_button = 0.6;
            }
            if (isset($slider_meta['breakpoint6_button']) && $slider_meta['breakpoint6_button'] != '') {
                $breakpoint6_button = esc_attr($slider_meta['breakpoint6_button']);
            } else {
                $breakpoint6_button = 0.5;
            }
            if (isset($slider_meta['breakpoint7_button']) && $slider_meta['breakpoint7_button'] != '') {
                $breakpoint7_button = esc_attr($slider_meta['breakpoint7_button']);
            } else {
                $breakpoint7_button = 0.4;
            }

            $responsive_coefficients_graphic_data = 'data-qodef_responsive_graphic_coefficients = "' . esc_attr($breakpoint1_graphic . ',' . $breakpoint2_graphic . ',' . $breakpoint3_graphic . ',' . $breakpoint4_graphic . ',' . $breakpoint5_graphic . ',' . $breakpoint6_graphic . ',' . $breakpoint7_graphic) . '"';
            $responsive_coefficients_title_data = 'data-qodef_responsive_title_coefficients = "' . esc_attr($breakpoint1_title . ',' . $breakpoint2_title . ',' . $breakpoint3_title . ',' . $breakpoint4_title . ',' . $breakpoint5_title . ',' . $breakpoint6_title . ',' . $breakpoint7_title) . '"';
            $responsive_coefficients_subtitle_data = 'data-qodef_responsive_subtitle_coefficients = "' . esc_attr($breakpoint1_subtitle . ',' . $breakpoint2_subtitle . ',' . $breakpoint3_subtitle . ',' . $breakpoint4_subtitle . ',' . $breakpoint5_subtitle . ',' . $breakpoint6_subtitle . ',' . $breakpoint7_subtitle) . '"';
            $responsive_coefficients_text_data = 'data-qodef_responsive_text_coefficients = "' . esc_attr($breakpoint1_text . ',' . $breakpoint2_text . ',' . $breakpoint3_text . ',' . $breakpoint4_text . ',' . $breakpoint5_text . ',' . $breakpoint6_text . ',' . $breakpoint7_text) . '"';
            $responsive_coefficients_button_data = 'data-qodef_responsive_button_coefficients = "' . esc_attr($breakpoint1_button . ',' . $breakpoint2_button . ',' . $breakpoint3_button . ',' . $breakpoint4_button . ',' . $breakpoint5_button . ',' . $breakpoint6_button . ',' . $breakpoint7_button) . '"';

            /* set responsive breakpoints - end */

            /* check if slider has auto start - start */
            $auto = "yes";
            if ($auto_start != "") {
                $auto = $auto_start;
            }

            if ($auto == "yes") {
                $auto_start_class = "qodef-auto-start";
            } else {
                $auto_start_class = "";
            }
            /* check if slider has auto start - end */

            /* check for alide animation time - start */
            if ($slide_animation_timeout != "") {
                $slide_animation_timeout = 'data-slide_animation_timeout="' . $slide_animation_timeout . '"';
            } else {
                $slide_animation_timeout = 'data-slide_animation_timeout="6000"';
            }
            /* check for alide animation time - end */

            /* check for slide animation type - start */
            switch ($animation_type) {
                case 'fade':
                    $animation_type_class = 'qodef-fade';
                    break;
                case 'slide-vertical-up':
                    $animation_type_class = 'qodef-vertical-up';
                    break;
                case 'slide-vertical-down':
                    $animation_type_class = 'qodef-vertical-down';
                    break;
                case 'slide-cover':
                    $animation_type_class = 'qodef-slide-cover';
                    break;
                default:
                    $animation_type_class = '';
            }
            /* check for slide animation type - end */

            /* set slider preloader - start */
            if($qode_startit_options['smooth_page_transitions'] == "yes" && $qode_startit_options['smooth_pt_spinner_type'] != "") {
                $ajax_loader = '<div class="qodef-st-loader"><div class="qodef-st-loader1">' . qode_startit_loading_spinners(true) . '</div></div>';
            }else{
                $ajax_loader = '<div class="qodef-st-loader"><div class="qodef-st-loader1">'. qode_startit_loading_spinner_pulse() .'</div></div>';
            }

            /* set slider preloader - end */

            /* set padding for slider arrows - start */
            $slider_arrows_padding = "padding-top: " . esc_attr($this->qode_startit_get_slider_navigation_padding()) . "px;";
            /* set padding for slider arrows - end */

            $html .= '<div id="qodef-' . esc_attr($slider) . '" ' . $anchor_data . '  ' . $responsiveness_data . ' ' . $responsive_coefficients_graphic_data . ' ' . $responsive_coefficients_title_data . ' ' . $responsive_coefficients_subtitle_data . ' ' . $responsive_coefficients_text_data . ' ' . $responsive_coefficients_button_data . ' class="carousel slide ' . esc_attr($animation_type_class . ' ' . $full_screen_class . ' ' . $responsive_height_class . ' ' . $height_class . ' ' . $auto_start_class . ' ' . $header_effect_class . ' ' . $navigation_effect_class . ' ' . $slider_numbers_class .  ' ' . $slider_thumbs_class) . ' " ' . $slide_animation_timeout . ' ' . $data_height . ' ' . $data_parallax_effect . ' '.qode_startit_get_inline_style($slide_holder_height).'><div class="qodef-slider-preloader">'.$ajax_loader.'</div>';
            $html .= '<div class="carousel-inner ' . esc_attr($slider_css_position_class) . '" '.qode_startit_get_inline_style($carouselinner_height).' '.$data_parallax_transform.'>';

            global $wp_query;
            query_posts($args);
            $found_slides = $wp_query->post_count;

            if (have_posts()) : $postCount = 0;
                while (have_posts()) : the_post();
                    //get slide title
                    $title = get_the_title();
                    //get slider type
                    $slide_type = get_post_meta(get_the_ID(), "qodef_slide_background_type", true);
                    //get slider image
                    $image = esc_url(get_post_meta(get_the_ID(), "qodef_slide_image", true));
                    //get slider overlay pattern
                    $image_overlay_pattern = esc_url(get_post_meta(get_the_ID(), "qodef_slide_overlay_image", true));
                    /* get slider thumbnail/graphic - start */
                    $thumbnail = esc_url(get_post_meta(get_the_ID(), "qodef_slide_thumbnail", true));
                    $thumbnail_attributes = qode_startit_get_attachment_meta_from_url($thumbnail, array('width','height'));
                    $thumbnail_attributes_width = '';
                    $thumbnail_attributes_height = '';
                    if($thumbnail_attributes == true){
                        $thumbnail_attributes_width = $thumbnail_attributes['width'];
                        $thumbnail_attributes_height = $thumbnail_attributes['height'];
                    }
                    $thumbnail_animation = get_post_meta(get_the_ID(), "qodef_slide_thumbnail_animation", true);
                    $thumbnail_link = "";
                    if (get_post_meta(get_the_ID(), "qodef_slide_thumbnail_link", true) != "") {
                        $thumbnail_link = esc_url(get_post_meta(get_the_ID(), "qodef_slide_thumbnail_link", true));
                    }

                    $thumbnail_class = "";
                    if ($thumbnail !== "") {
                        $thumbnail_class = "qodef-has-thumbnail";
                    }
                    /* get slider thumbnail/graphic - end */

                    /* get slider video files - start */
                    $video_webm = esc_url(get_post_meta(get_the_ID(), "qodef_slide_video_webm", true));
                    $video_mp4 = esc_url(get_post_meta(get_the_ID(), "qodef_slide_video_mp4", true));
                    $video_ogv = esc_url(get_post_meta(get_the_ID(), "qodef_slide_video_ogv", true));
                    $video_image = esc_url(get_post_meta(get_the_ID(), "qodef_slide_video_image", true));
                    $video_overlay = get_post_meta(get_the_ID(), "qodef_slide_video_overlay", true);
                    $video_overlay_image = esc_url(get_post_meta(get_the_ID(), "qodef_slide_video_overlay_image", true));
                    /* get slider video files - end */

                    /* slider content settings - start */
                    $content_animation = get_post_meta(get_the_ID(), "qodef_slide_content_animation", true);
                    $content_animation_direction = get_post_meta(get_the_ID(), "qodef_slide_content_animation_direction", true);

                    $slide_content_style = "";
                    if (get_post_meta(get_the_ID(), "qodef_slide_text_content_top_margin", true) != "") {
                        $slide_content_style .= "margin-top: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_content_top_margin", true)) . "px;";
                    }
                    if (get_post_meta(get_the_ID(), "qodef_slide_text_content_bottom_margin", true) != "") {
                        $slide_content_style .= "margin-bottom: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_content_bottom_margin", true)) . "px;";
                    }
                    /* slider content settings - end */

                    /* slider thumb/graphic settings - start */
					$slide_graphic_style = "";
					if (get_post_meta(get_the_ID(), "qodef_slide_graphic_top_padding", true) != "") {
                        $slide_graphic_style .= "padding-top: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_top_padding", true)) . "px;";
                    }
					if (get_post_meta(get_the_ID(), "qodef_slide_graphic_bottom_padding", true) != "") {
                        $slide_graphic_style .= "padding-bottom: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_bottom_padding", true)) . "px;";
                    }

                    $animate_image_class = "";
                    $animate_image_data = "";
                    if (get_post_meta(get_the_ID(), "qodef_enable_image_animation", true) == "yes") {
                        $animate_image_class .= "qodef-animate-image ";
                        $animate_image_class .= get_post_meta(get_the_ID(), "qodef_enable_image_animation_type", true);
                        $animate_image_data .= "data-qodef_animate_image='".get_post_meta(get_the_ID(), "qodef_enable_image_animation_type", true)."'";
                    }

                    /* slider thumb/graphic settings - end */

                    /* slider elements alignment - start */
                    $graphic_alignment = get_post_meta(get_the_ID(), "qodef_slide_graphic_alignment", true);
                    $content_alignment = get_post_meta(get_the_ID(), "qodef_slide_content_alignment", true);
                    $separate_text_graphic = get_post_meta(get_the_ID(), "qodef_slide_separate_text_graphic", true);
                    /* slider elements alignment - end */


                    /* slider elements positioning - start */
                    $content_full_width_class = "";
                    if (get_post_meta(get_the_ID(), "qodef_slide_content_full_width", true) == "yes" && get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "no") {
                        $content_full_width_class = "qodef-slide-full-width";
                    }else if(get_post_meta(get_the_ID(), "qodef_slide_vertical_content_full_width", true) == "yes" && get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
						$content_full_width_class = "qodef-slide-full-width";
					}

                    if (get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle_type", true) == 'window_top') {
                        $slide_item_padding_value = 0;
                    } else {
                        $slide_item_padding_value = $this->qode_startit_get_slider_item_padding();
                    }

                    $content_vertical_middle_position_class = "";
                    $slide_item_padding = "";

                    if (get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes") {
                        $content_vertical_middle_position_class = "qodef-content-vertical-middle";
                        $slide_item_padding = "padding-top: " . esc_attr($slide_item_padding_value) . "px;";
						$vertical_content_width = "";
						$vertical_content_xaxis = "";
						
						$content_width = "";
                        $content_xaxis = "";
                        $content_yaxis_start = "";
                        $content_yaxis_end = "";
                        $graphic_width = "";
                        $graphic_xaxis = "";
                        $graphic_yaxis_start = "";
                        $graphic_yaxis_end = "";
						
						if(get_post_meta(get_the_ID(), "qodef_slide_vertical_content_width", true) != ""){
							$vertical_content_width = "width:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_vertical_content_width", true)) . "%; position:relative; ";
						}
						if (get_post_meta(get_the_ID(), "qodef_slide_vertical_content_left", true) != "") {
                            $vertical_content_xaxis = "left:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_vertical_content_left", true)) . "%;";
                        }else if (get_post_meta(get_the_ID(), "qodef_slide_vertical_content_right", true) != "") {
                                $vertical_content_xaxis = "right:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_vertical_content_right", true)) . "%;";
                        }
                    } else {

                        if (get_post_meta(get_the_ID(), "qodef_slide_content_width", true) != "") {
                            $content_width = "width:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_width", true)) . "%;";
                        } else {
                            $content_width = "width:80%;";
                        }

                        if (get_post_meta(get_the_ID(), "qodef_slide_content_left", true) != "") {
                            $content_xaxis = "left:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_left", true)) . "%;";
                        } else {
                            if (get_post_meta(get_the_ID(), "qodef_slide_content_right", true) != "") {
                                $content_xaxis = "right:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_right", true)) . "%;";
                            } else {
                                $content_xaxis = "left: 10%;";
                            }
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_content_top", true) != "") {
                            $content_yaxis_start = "top:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_top", true)) . "%;";
                            $content_yaxis_end = "top:" . (esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_top", true)) - 10) . "%;";
                        } else {
                            if (get_post_meta(get_the_ID(), "qodef_slide_content_bottom", true) != "") {
                                $content_yaxis_start = "bottom:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_bottom", true)) . "%;";
                                $content_yaxis_end = "bottom:" . (esc_attr(get_post_meta(get_the_ID(), "qodef_slide_content_bottom", true)) + 10) . "%;";
                            } else {
                                $content_yaxis_start = "top: 35%;";
                                $content_yaxis_end = "top: 10%;";
                            }
                        }

                        if ($separate_text_graphic == 'yes' && get_post_meta(get_the_ID(), "qodef_slide_text_width", true) != "") {
                            $content_width = "width:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_width", true)) . "%;";
                        }

                        if (get_post_meta(get_the_ID(), "qodef_slide_graphic_width", true) != "") {
                            $graphic_width = "width:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_width", true)) . "%;";
                        } else {
                            $graphic_width = "width:50%;";
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_graphic_left", true) != "") {
                            $graphic_xaxis = "left:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_left", true)) . "%;";
                        } else {
                            if (get_post_meta(get_the_ID(), "qodef_slide_graphic_right", true) != "") {
                                $graphic_xaxis = "right:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_right", true)) . "%;";
                            } else {
                                $graphic_xaxis = "left: 25%;";
                            }
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_graphic_top", true) != "") {
                            $graphic_yaxis_start = "top:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_top", true)) . "%;";
                            $graphic_yaxis_end = "top:" . (esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_top", true)) - 10) . "%;";
                        } else {
                            if (get_post_meta(get_the_ID(), "qodef_slide_graphic_bottom", true) != "") {
                                $graphic_yaxis_start = "bottom:" . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_bottom", true)) . "%;";
                                $graphic_yaxis_end = "bottom:" . (esc_attr(get_post_meta(get_the_ID(), "qodef_slide_graphic_bottom", true)) + 10) . "%;";
                            } else {
                                $graphic_yaxis_start = "top: 30%;";
                                $graphic_yaxis_end = "top: 10%;";
                            }
                        }
                    }
                    /* slider elements positioning - end */

                    /* slide on scroll animation - start */

                    //fefault values for data start and data end animation
                    $data_start_amount = '0';
                    $data_end_amount = '300';
                    $data_start_custom_style = ' opacity: 1;';
                    $data_end_custom_style = ' opacity: 0;';

                    $slide_data_start = ' data-' . $data_start_amount . '="' . $data_start_custom_style . ' ' . $content_width . ' ' . $content_xaxis . ' ' . $content_yaxis_start . '"';
                    $slide_data_end = ' data-' . $data_end_amount . '="' . $data_end_custom_style . ' ' . $content_xaxis . ' ' . $content_yaxis_end . '"';

                    $graphic_data_start = ' data-' . $data_start_amount . '="' . $data_start_custom_style . ' ' . $graphic_width . ' ' . $graphic_xaxis . ' ' . $graphic_yaxis_start . '"';
                    $graphic_data_end = ' data-' . $data_end_amount . '="' . $data_end_custom_style . ' ' . $graphic_xaxis . ' ' . $graphic_yaxis_end . '"';

                    /* slide on scroll animation - end */

                    /* check if slide has header style settings - start */
                    $header_style = "";
                    if (get_post_meta(get_the_ID(), "qodef_slide_header_style", true) != "") {
                        $header_style = get_post_meta(get_the_ID(), "qodef_slide_header_style", true);
                    }
                    /* check if slide has header style settings - end */


                    $html .= '<div class="item ' . $header_style . ' ' . $thumbnail_class . ' ' . $content_vertical_middle_position_class . ' ' . $content_full_width_class . ' '.$animate_image_class.'" style="' . $slide_height . ' ' . $slide_item_padding . '" '.$animate_image_data.'>';

                    /* render video or image for slide item - start */
                    if ($slide_type == 'video') {

                        $html .= '<div class="qodef-video"><div class="qodef-mobile-video-image" '.qode_startit_get_inline_style('background-image: url(' . esc_url($video_image) . ')').'></div><div class="qodef-video-overlay';
                        if ($video_overlay == "yes") {
                            $html .= ' active';
                        }
                        $html .= '"';
                        if ($video_overlay_image != "") {
                            $html .= ' style="background-image:url(' . esc_url($video_overlay_image) . ');"';
                        }
                        $html .= '>';
                        if ($video_overlay_image != "") {
                            $html .= '<img src="' . esc_url($video_overlay_image) . '" alt="" />';
                        } else {
                            $html .= '<img src="' . esc_url(get_template_directory_uri()) . '/assets/css/img/pixel-video.png" alt="" />';
                        }
                        $html .= '</div><div class="qodef-video-wrap">';
                        $html .= '<video class="video" width="1920" height="800" poster="' . esc_url($video_image) . '" controls="controls" preload="auto" loop autoplay muted>';
                        if (!empty($video_webm)) {
                            $html .= '<source type="video/webm" src="' . esc_url($video_webm) . '">';
                        }
                        if (!empty($video_mp4)) {
                            $html .= '<source type="video/mp4" src="' . esc_url($video_mp4) . '">';
                        }
                        if (!empty($video_ogv)) {
                            $html .= '<source type="video/ogg" src="' . esc_url($video_ogv) . '">';
                        }
                        $html .='<object width="320" height="240" type="application/x-shockwave-flash" data="' . esc_url(get_template_directory_uri()) . '/assets/js/flashmediaelement.swf">
													<param name="movie" value="' . esc_url(get_template_directory_uri()) . '/assets/js/flashmediaelement.swf" />
													<param name="flashvars" value="controls=true&amp;file=' . esc_url($video_mp4) . '" />
													<img src="' . esc_url($video_image) . '" width="1920" height="800" title="No video playback capabilities" alt="Video thumb" />
											</object>
									</video>
							</div></div>';
                    } else {
                        $html .= '<div class="qodef-image" '.qode_startit_get_inline_style('background-image:url(' . esc_url($image) . ')').'>';
                        if ($slider_thumbs == 'no') {
                            $html .= '<img src="' . esc_url($image) . '" alt="' . esc_attr($title) . '">';
                        }

                        if ($image_overlay_pattern !== "") {
                            $html .= '<div class="qodef-image-pattern" '.qode_startit_get_inline_style('background: url(' . esc_url($image_overlay_pattern) . ') repeat 0 0').'></div>';
                        }
                        $html .= '</div>';
                    }
                    /* render video or image for slide item - end */

                    /* prepare slide graphic which will be used below - start */
                    $html_thumb = "";
                    if ($thumbnail != "") {
                        $html_thumb .= '<div style="' . esc_attr($slide_graphic_style) . '">';
                        $html_thumb .= '<div class="qodef-thumb ' . esc_attr($thumbnail_animation) . '">';
                        if ($thumbnail_link != "") {
                            $html_thumb .= '<a href="' . esc_url($thumbnail_link) . '" target="_self">';
                        }

                        $html_thumb .= '<img data-width="'.esc_attr($thumbnail_attributes_width).'" data-height="'.esc_attr($thumbnail_attributes_height).'" src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '">';

                        if ($thumbnail_link != "") {
                            $html_thumb .= '</a>';
                        }
                        $html_thumb .= '</div></div>';
                    }
                    /* prepare slide graphic which will be used below - end */


                    $html_text = "";
                    $html_text .= '<div class="qodef-text ' . esc_attr($content_animation . ' ' . $content_animation_direction) . ' " style="' . esc_attr($slide_content_style) . '">';

                    /* prepare slide subtitle - start */
                    if (get_post_meta(get_the_ID(), "qodef_slide_subtitle", true) != "") {

                        $slide_subtitle_style_array = array();
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_subtitle_color", true)) != "") {
                            $slide_subtitle_style_array[] = "color: " . esc_attr($meta_temp);
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_subtitle_font_size", true)) != "") {
                            $slide_subtitle_style_array[] = "font-size: " . $meta_temp."px";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_subtitle_line_height", true)) != "") {
                            $slide_subtitle_style_array[] = "line-height: " . $meta_temp . "px";
                        }
                        if ((($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_subtitle_font_family", true)) !== "-1") && ($meta_temp !== "")) {
                            $slide_subtitle_style_array[] = "font-family: '" . esc_attr(str_replace('+', ' ', $meta_temp)) . "'";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_subtitle_font_style", true)) != "") {
                            $slide_subtitle_style_array[] = "font-style: " . esc_attr($meta_temp) . "";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_subtitle_font_weight", true)) != "") {
                            $slide_subtitle_style_array[] = "font-weight: " . esc_attr($meta_temp) . "";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_letter_spacing', true) )!== '') {
                            $slide_subtitle_style_array[] = 'letter-spacing: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_text_transform', true) )!== '') {
                            $slide_subtitle_style_array[] = 'text-transform: ' . esc_attr($meta_temp) . '';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_margin_bottom', true)) != '') {
                            $slide_subtitle_style_array[] = 'margin-bottom: ' . esc_attr($meta_temp) . 'px';
                        }

                        $slide_subtitle_span_style_array = array();
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_background_color', true)) !== '') {
                            $slide_subtitle_bg_color = esc_attr($meta_temp);
                            if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_bg_color_transparency', true)) != '') {
                                $slide_subtitle_bg_transparency = esc_attr($meta_temp);
                            } else {
                                $slide_subtitle_bg_transparency = 1;
                            }
                            $slide_subtitle_style_array[] = 'background-color: ' . esc_attr(qode_startit_rgba_color($slide_subtitle_bg_color, $slide_subtitle_bg_transparency)) . '';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_padding_top', true)) != '') {
                            $slide_subtitle_span_style_array[] = 'padding-top: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_padding_right', true)) != '') {
                            $slide_subtitle_span_style_array[] = 'padding-right: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_padding_bottom', true)) != '') {
                            $slide_subtitle_span_style_array[] = 'padding-bottom: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_subtitle_padding_left', true)) != '') {
                            $slide_subtitle_span_style_array[] = 'padding-left: ' . esc_attr($meta_temp) . 'px';
                        }

                        $html_text .= '<div class="qodef-el">';
                        $html_text .= '<h3 class="qodef-slide-subtitle" '.qode_startit_get_inline_style($slide_subtitle_style_array). '><span '.qode_startit_get_inline_style($slide_subtitle_span_style_array). '>' . wp_kses_post(get_post_meta(get_the_ID(), 'qodef_slide_subtitle', true)) . '</span></h3>';
                        $html_text .= '</div>';
                    }
                    /* prepare slide subtitle - end */

                    /* prepare slide tile - start */
                    if (get_post_meta(get_the_ID(), "qodef_slide_hide_title", true) == 'no') {

                        $slide_title_style_array = array();
                        $slide_title__link_style_array = array();
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_title_color", true)) != "") {
                            $slide_title_style_array[] = "color: " . esc_attr($meta_temp) . "";
                            $slide_title__link_style_array[] = "color: " . esc_attr($meta_temp) . "";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_title_font_size", true)) != "") {
                            $slide_title_style_array[] = "font-size: " . esc_attr($meta_temp) . "px";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_title_line_height", true)) != "") {
                            $slide_title_style_array[] = "line-height: " . esc_attr($meta_temp) . "px";
                        }
                        if ((($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_title_font_family", true)) !== "-1") && ($meta_temp !== "")) {
                            $slide_title_style_array[] = "font-family: '" . esc_attr(str_replace('+', ' ', $meta_temp)) . "'";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_title_font_style", true)) != "") {
                            $slide_title_style_array[] = "font-style: " . esc_attr($meta_temp) . "";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_title_font_weight", true)) != "") {
                            $slide_title_style_array[] = "font-weight: " . esc_attr($meta_temp) . "";
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_letter_spacing', true) )!== '') {
                            $slide_title_style_array[] = 'letter-spacing: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_text_transform', true) )!== '') {
                            $slide_title_style_array[] = 'text-transform: ' . esc_attr($meta_temp) . '';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_margin_bottom', true)) != '') {
                            $slide_title_style_array[] = 'margin-bottom: ' . esc_attr($meta_temp) . 'px';
                        }

                        $slide_title_span_style_array = array();
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_background_color', true)) !== '') {
                            $slide_title_bg_color = esc_attr($meta_temp);
                            if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_bg_color_transparency', true)) != '') {
                                $slide_title_bg_transparency = esc_attr($meta_temp);
                            } else {
                                $slide_title_bg_transparency = 1;
                            }
                            $slide_title_span_style_array[] = 'background-color: ' . esc_attr(qode_startit_rgba_color($slide_title_bg_color, $slide_title_bg_transparency)) . '';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_padding_top', true)) != '') {
                            $slide_title_span_style_array[] = 'padding-top: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_padding_right', true)) != '') {
                            $slide_title_span_style_array[] = 'padding-right: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_padding_bottom', true)) != '') {
                            $slide_title_span_style_array[] = 'padding-bottom: ' . esc_attr($meta_temp) . 'px';
                        }
                        if (($meta_temp = get_post_meta(get_the_ID(), 'qodef_slide_title_padding_left', true)) != '') {
                            $slide_title_span_style_array[] = 'padding-left: ' . esc_attr($meta_temp) . 'px';
                        }

                        $html_text .= '<div class="qodef-el">';
                        $html_text .= '<h2 class="qodef-slide-title" '.qode_startit_get_inline_style($slide_title_style_array).'>';

                        if(get_post_meta(get_the_ID(), "qodef_slide_title_link", true) != '') {
                            $html_text .= '<a '.qode_startit_get_inline_style($slide_title__link_style_array).' href="'.esc_url(get_post_meta(get_the_ID(), "qodef_slide_title_link", true)).'" target="'.esc_attr(get_post_meta(get_the_ID(), "qodef_slide_title_target", true)).'">';
                        }
                        $html_text .= '<span '.qode_startit_get_inline_style($slide_title_span_style_array).'>'.wp_kses_post($title).'</span>';
                        if(get_post_meta(get_the_ID(), "qodef_slide_title_link", true) != '') {
                            $html_text .= '</a>';
                        }

                        $html_text .= '</h2></div>';
                    }
                    /* prepare slide tile - end */

                    /* prepare slide text - start */
                    if (get_post_meta(get_the_ID(), "qodef_slide_text", true) != "") {

                        $slide_text_style_array = array();
                        $slide_text_span_style_array = array();

                        if (get_post_meta(get_the_ID(), "qodef_slide_text_color", true) != "") {
                            $slide_text_style_array[] = "color: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_color", true)) . "";
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_text_font_size", true) != "") {
                            $slide_text_style_array[] = "font-size: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_font_size", true)) . "px";
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_text_line_height", true) != "") {
                            $slide_text_style_array[] = "line-height: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_line_height", true)) . "px";
                        }
                        if ((($meta_temp = get_post_meta(get_the_ID(), "qodef_slide_text_font_family", true)) !== "-1") && ($meta_temp !== "")) {
                            $slide_text_style_array[] = "font-family: '" . esc_attr(str_replace('+', ' ', $meta_temp)) . "'";
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_text_font_style", true) != "") {
                            $slide_text_style_array[] = "font-style: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_font_style", true)) . "";
                        }
                        if (get_post_meta(get_the_ID(), "qodef_slide_text_font_weight", true) != "") {
                            $slide_text_style_array[] = "font-weight: " . esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_font_weight", true)) . "";
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_letter_spacing', true) !== '') {
                            $slide_text_style_array[] = 'letter-spacing: ' . esc_attr(get_post_meta(get_the_ID(), 'qodef_slide_text_letter_spacing', true)) . 'px';
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_text_transform', true) !== '') {
                            $slide_text_style_array[] = 'text-transform: ' . esc_attr(get_post_meta(get_the_ID(), 'qodef_slide_text_text_transform', true)) . '';
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_background_color', true) !== '') {
                            $slide_text_bg_color = esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_background_color", true));
                            if (get_post_meta(get_the_ID(), 'qodef_slide_text_bg_color_transparency', true) != '') {
                                $slide_text_bg_transparency = esc_attr(get_post_meta(get_the_ID(), "qodef_slide_text_bg_color_transparency", true));
                            } else {
                                $slide_text_bg_transparency = 1;
                            }
                            $slide_text_span_style_array[] = 'background-color: ' . esc_attr(qode_startit_rgba_color($slide_text_bg_color, $slide_text_bg_transparency)) . '';
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_padding_top', true) != '') {
                            $slide_text_span_style_array[] = 'padding-top: ' . esc_attr(get_post_meta(get_the_ID(), 'qodef_slide_text_padding_top', true)) . 'px';
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_padding_right', true) != '') {
                            $slide_text_span_style_array[] = 'padding-right: ' . esc_attr(get_post_meta(get_the_ID(), 'qodef_slide_text_padding_right', true)) . 'px';
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_padding_bottom', true) != '') {
                            $slide_text_span_style_array[] = 'padding-bottom: ' . esc_attr(get_post_meta(get_the_ID(), 'qodef_slide_text_padding_bottom', true)) . 'px';
                        }
                        if (get_post_meta(get_the_ID(), 'qodef_slide_text_padding_left', true) != '') {
                            $slide_text_span_style_array[] = 'padding-left: ' . esc_attr(get_post_meta(get_the_ID(), 'qodef_slide_text_padding_left', true)) . 'px';
                        }

                        $html_text .= '<div class="qodef-el">';
                        $html_text .= '<h3 class="qodef-slide-text" '.qode_startit_get_inline_style($slide_text_style_array).'><span '.qode_startit_get_inline_style($slide_text_span_style_array).'>'.wp_kses_post(get_post_meta(get_the_ID(), "qodef_slide_text", true)).'</span></h3>';
                        $html_text .= '</div>';
                    }
                    /* prepare slide text - end */


                    /* prepare slide buttons - start */

                    //check if first button should be displayed
                    $is_first_button_shown = get_post_meta(get_the_ID(), "qodef_slide_button_label", true) != "" && get_post_meta(get_the_ID(), "qodef_slide_button_link", true) != "";

                    //check if second button should be displayed
                    $is_second_button_shown = get_post_meta(get_the_ID(), "qodef_slide_button_label2", true) != "" && get_post_meta(get_the_ID(), "qodef_slide_button_link2", true) != "";

                    //does any button should be displayed?
                    $is_any_button_shown = $is_first_button_shown || $is_second_button_shown;

                    if ($is_any_button_shown) {
                        $html_text .= '<div class="qodef-el">';
                        $html_text .= '<div class="qodef-slide-buttons-holder">';
                    }


                    if ($is_first_button_shown) {
                        $slide_button_target = "_self";
                        if (get_post_meta(get_the_ID(), "qodef_slide_button_target", true) != "") {
                            $slide_button_target = get_post_meta(get_the_ID(), "qodef_slide_button_target", true);
                        }

                        $html_text .= '<a class="qodef-btn-hover-animation qodef-btn qodef-btn-medium qodef-btn-solid" href="' . esc_url(get_post_meta(get_the_ID(), "qodef_slide_button_link", true)) . '" target="' . esc_attr($slide_button_target) . '"><span class="qodef-animation-overlay"></span><span class="qodef-btn-text">' . esc_html(get_post_meta(get_the_ID(), "qodef_slide_button_label", true)) . '</span></a>';
                    }

                    if ($is_second_button_shown) {
                        $slide_button_target2 = "_self";
                        if (get_post_meta(get_the_ID(), "qodef_slide_button_target2", true) != "") {
                            $slide_button_target2 = get_post_meta(get_the_ID(), "qodef_slide_button_target2", true);
                        }

                        $html_text .= '<a class="qodef-btn-hover-animation qodef-btn qodef-btn-medium qodef-btn-default" href="' . esc_url(get_post_meta(get_the_ID(), "qodef_slide_button_link2", true)) . '" target="' . esc_attr($slide_button_target2) . '"><span class="qodef-animation-overlay"></span><span class="qodef-btn-text">' . esc_html(get_post_meta(get_the_ID(), "qodef_slide_button_label2", true)) . '</span></a>';
                    }

                    if ($is_any_button_shown) {
                        $html_text .= '</div></div>'; //close div.qodef-slide-button-holder
                    }

                    /* prepare slide buttons - start */

                    $html_text .= '</div>';

                    $html .= '<div class="qodef-slider-content-outer">';

                    if ($separate_text_graphic != 'yes') {
                        $html .= '<div class="qodef-slider-content ' . esc_attr($content_alignment) .'" '.qode_startit_get_inline_style($content_width . $content_xaxis . $content_yaxis_start).' '.$slide_data_start.' '.$slide_data_end.'>';
							if(get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
								$html .= '<div class="qodef-slider-content-inner ' . esc_attr($content_animation . ' ' . $content_animation_direction) .'" '.qode_startit_get_inline_style($vertical_content_width . $vertical_content_xaxis).'>';
							}
							$html .= $html_thumb;
							$html .= $html_text;
							if(get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
								$html .= '</div>';
							}
                        $html .= '</div>';
                    } else {
                        $html .= '<div class="qodef-slider-content qodef-graphic-content ' . esc_attr($graphic_alignment) . '" '.qode_startit_get_inline_style($graphic_width . $graphic_xaxis . $graphic_yaxis_start).' '. $graphic_data_start .' '.$graphic_data_end.'>';
							if(get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
								$html .= '<div class="qodef-slider-content-inner" '.qode_startit_get_inline_style($vertical_content_width . $vertical_content_xaxis).'>';
							}
							$html .= $html_thumb;
							if(get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
								$html .= '</div>';
							}
                        $html .= '</div>';
                        $html .= '<div class="qodef-slider-content ' . esc_attr($content_alignment) . '" '.qode_startit_get_inline_style($content_width . $content_xaxis . $content_yaxis_start).' '.$slide_data_start.' '.$slide_data_end.'>';
							if(get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
								$html .= '<div class="qodef-slider-content-inner" '.qode_startit_get_inline_style($vertical_content_width . $vertical_content_xaxis).'>';
							}
							$html .= $html_text;
							if(get_post_meta(get_the_ID(), "qodef_slide_content_vertical_middle", true) == "yes"){
								$html .= '</div>';
							}
                        $html .= '</div>';
                    }

                    $html .= '</div>'; //close div.qodef-slide-content-outer
                    $html .= '</div>'; //close div.item

                    $postCount++;
                endwhile;
            else:
                $html .= __('Sorry, no slides matched your criteria.', 'select-core');
            endif;
            wp_reset_query();

            $html .= '</div>'; //close div.carousel-inner

            if ($found_slides > 1) {
                if ($show_navigation_circles == "yes") {

                    $html .= '<ol class="carousel-indicators" data-start="opacity: 1;" data-300="opacity:0;">';

                    query_posts($args);
                    if (have_posts()) : $postCount = 0;
                        while (have_posts()) : the_post();

                            $html .= '<li data-target="#qodef-' . esc_attr($slider) . '" data-slide-to="' . esc_attr($postCount) . '"';
                            if ($postCount == 0) {
                                $html .= ' class="active"';
                            }
                            $html .= '></li>';

                            $postCount++;
                        endwhile;
                    else:
                        $html .= __('Sorry, no posts matched your criteria.', 'select-core');
                    endif;

                    wp_reset_query();
                    $html .= '</ol>';
                }

                if ($show_navigation_arrows == "yes") {

                    $html .= '<div class="qodef-controls-holder">';

                    $html .= '<a class="left carousel-control" style="'.$slider_arrows_padding.'" href="#qodef-' . esc_attr($slider) . '" data-slide="prev" data-start="opacity: 1;" data-300="opacity:0;">';
                        if ($slider_thumbs == 'yes') {
                            $html .= '<span class="qodef-thumb-holder"><span class="img"></span></span>';
                        }
                        $html .= '<span class="qodef-prev-nav">';
                            if($slider_numbers == 'yes') {
                                $html .= '<span class="qodef-numbers"><span class="prev"></span><span class="max-number"> / ' . esc_html($postCount) . '</span></span>';
                            }
                            $html .= '<span class="fa fa-chevron-left"></span>';
                        $html .= '</span>';
                    $html .= '</a>';

                    $html .= '<a class="right carousel-control" style="'.$slider_arrows_padding.'" href="#qodef-' . esc_attr($slider) . '" data-slide="next" data-start="opacity: 1;" data-300="opacity:0;">';
                        if ($slider_thumbs == 'yes') {
                            $html .= '<span class="qodef-thumb-holder"><span class="img"></span></span>';
                        }

                        $html .= '<span class="qodef-next-nav">';
                            if($slider_numbers == 'yes') {
                                $html .= '<span class="qodef-numbers"> <span class="next"></span><span class="max-number"> / ' . esc_html($postCount) . '</span></span>';
                            }
                            $html .= '<span class="fa fa-chevron-right"></span>';
                        $html .= '</span>';
                    $html .= '</a>';

                    $html .= '</div>'; //close of div.qodef-controls-holder
                }
            }
            $html .= '</div>'; //close of div.carousel
        }

        return $html;
    }

    /**
     * Function that returns slider item pading
     **/
    function qode_startit_get_slider_item_padding() {
        return apply_filters('qode_startit_slider_item_padding', 0);
    }

    /**
     * Function that returns slider navigation pading
     **/
    function qode_startit_get_slider_navigation_padding() {
        return apply_filters('qode_startit_slider_navigation_padding', 0);
    }
}