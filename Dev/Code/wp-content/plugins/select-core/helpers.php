<?php

if(!function_exists('qode_core_version_class')) {
    /**
     * Adds plugins version class to body
     * @param $classes
     * @return array
     */
    function qode_core_version_class($classes) {
        $classes[] = 'qode-core-'.QODE_CORE_VERSION;

        return $classes;
    }

    add_filter('body_class', 'qode_core_version_class');
}

if(!function_exists('qode_core_theme_installed')) {
    /**
     * Checks whether theme is installed or not
     * @return bool
     */
    function qode_core_theme_installed() {
        return defined('QODE_ROOT');
    }
}

if (!function_exists('qode_core_get_carousel_slider_array')){
    /**
     * Function that returns associative array of carousels,
     * where key is term slug and value is term name
     * @return array
     */
    function qode_core_get_carousel_slider_array() {
        $carousels_array = array();
        $terms = get_terms('carousels_category');

        if (is_array($terms) && count($terms)) {
            $carousels_array[''] = '';
            foreach ($terms as $term) {
                $carousels_array[$term->slug] = $term->name;
            }
        }

        return $carousels_array;
    }
}

if(!function_exists('qode_core_get_carousel_slider_array_vc')) {
    /**
     * Function that returns array of carousels formatted for Visual Composer
     *
     * @return array array of carousels where key is term title and value is term slug
     *
     * @see qode_core_get_carousel_slider_array
     */
    function qode_core_get_carousel_slider_array_vc() {
        return array_flip(qode_core_get_carousel_slider_array());
    }
}

if(!function_exists('qode_core_get_shortcode_module_template_part')) {
	/**
	 * Loads module template part.
	 *
	 * @param string $shortcode name of the shortcode folder
	 * @param string $template name of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @see qode_startit_get_template_part()
	 */
	function qode_core_get_shortcode_module_template_part($shortcode,$template, $slug = '', $params = array()) {

		//HTML Content from template
		$html = '';
		$template_path = QODE_CORE_CPT_PATH.'/'.$shortcode.'/shortcodes/templates';
		
		$temp = $template_path.'/'.$template;
		if(is_array($params) && count($params)) {
			extract($params);
		}
		
		$template = '';

		if($temp !== '') {
			if($slug !== '') {
                $temp = "{$temp}-{$slug}";
			}
			$template = $temp.'.php';
		}
		if($template) {
			ob_start();
			include($template);
			$html = ob_get_clean();
		}

		return $html;
	}
}

if(!function_exists('qode_core_set_portfolio_ajax_url')){
	/**
     * load themes ajax functionality
     * 
     */
	function qode_core_set_portfolio_ajax_url() {
		echo '<script type="application/javascript">var qodeCoreAjaxUrl = "'.admin_url('admin-ajax.php').'"</script>';
	}
	add_action('wp_enqueue_scripts', 'qode_core_set_portfolio_ajax_url');
	
}
/**
	 * Loads more function for portfolio.
	 *
	 */
if(!function_exists('qode_core_portfolio_ajax_load_more')){
	
	function qode_core_portfolio_ajax_load_more(){
	
	$return_obj = array();
	$shortcode_params = array();
	$activeFilterCat = '';
	if (!empty($_POST['type'])) {
        $shortcode_params['type'] = $_POST['type'];
    }
	if (!empty($_POST['columns'])) {
        $shortcode_params['columns'] = $_POST['columns'];
    }
	if (!empty($_POST['gridSize'])) {
        $shortcode_params['gridSize'] = $_POST['gridSize'];
    }
	if (!empty($_POST['orderBy'])) {
        $shortcode_params['order_by'] = $_POST['orderBy'];
    }
	if (!empty($_POST['order'])) {
        $shortcode_params['order'] = $_POST['order'];
    }
	if (!empty($_POST['number'])) {
        $shortcode_params['number'] = $_POST['number'];
    }
	if (!empty($_POST['filter'])) {
        $shortcode_params['filter'] = $_POST['filter'];
    }
	if (!empty($_POST['filterOrderBy'])) {
        $shortcode_params['filter_order_by'] = $_POST['filterOrderBy'];
    }
	if (!empty($_POST['category'])) {
        $shortcode_params['category'] = $_POST['category'];
    }
	if (!empty($_POST['selectedProjectes'])) {
        $shortcode_params['selected_projectes'] = $_POST['selectedProjectes'];
    }
	if (!empty($_POST['showLoadMore'])) {
        $shortcode_params['show_load_more'] = $_POST['showLoadMore'];
    }
	if (!empty($_POST['titleTag'])) {
        $shortcode_params['title_tag'] = $_POST['titleTag'];
    }
	if (!empty($_POST['nextPage'])) {
        $shortcode_params['next_page'] = $_POST['nextPage'];
    }
	if (!empty($_POST['activeFilterCat'])) {
        $shortcode_params['active_filter_cat'] = $_POST['activeFilterCat'];
    }
	
	$html = '';
	
	$port_list = new \QodeCore\CPT\Portfolio\Shortcodes\PortfolioList();
	$query_array = $port_list->getQueryArray($shortcode_params);
	$query_results = new \WP_Query($query_array);
	
	if($query_results->have_posts()):			
		while ( $query_results->have_posts() ) : $query_results->the_post(); 

			$shortcode_params['current_id'] = get_the_ID();
			$shortcode_params['thumb_size'] = $port_list->getImageSize($shortcode_params);
			$shortcode_params['icon_html'] = $port_list->getPortfolioIconsHtml($shortcode_params);
			$shortcode_params['category_html'] = $port_list->getItemCategoriesHtml($shortcode_params);
			$shortcode_params['categories'] = $port_list->getItemCategories($shortcode_params);
			$shortcode_params['item_link'] = $port_list->getItemLink($shortcode_params);

			$html .= qode_core_get_shortcode_module_template_part('portfolio',$shortcode_params['type'], '', $shortcode_params);	
			
		endwhile;
		else: 			
			$html .= '<p>'. __('Sorry, no posts matched your criteria.', 'select-core') .'</p>';
		endif;
		
	$return_obj = array(
		'html' => $html,
	);

	
	echo json_encode($return_obj); exit;
}
}


add_action('wp_ajax_nopriv_qode_core_portfolio_ajax_load_more', 'qode_core_portfolio_ajax_load_more');
add_action( 'wp_ajax_qode_core_portfolio_ajax_load_more', 'qode_core_portfolio_ajax_load_more' );