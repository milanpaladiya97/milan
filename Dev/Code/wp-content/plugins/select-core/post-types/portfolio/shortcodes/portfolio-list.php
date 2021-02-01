<?php
namespace QodeCore\CPT\Portfolio\Shortcodes;

use QodeCore\Lib;

/**
 * Class PortfolioList
 * @package QodeCore\CPT\Portfolio\Shortcodes
 */
class PortfolioList implements Lib\ShortcodeInterface {
    /**
     * @var string
     */
    private $base;

    public function __construct() {
        $this->base = 'qodef_portfolio_list';

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
     * @see vc_map
     */
    public function vcMap() {
        if(function_exists('vc_map')) {

            $icons_array= array();
            if(qode_core_theme_installed()) {
                $icons_array = \QodeStartitIconCollections::get_instance()->getVCParamsArray();
            }

            vc_map( array(
                'name' => 'Portfolio List',
                'base' => $this->getBase(),
                'category' => 'by SELECT',
                'icon' => 'icon-wpb-portfolio extended-custom-icon',
                'allowed_container_element' => 'vc_row',
                'params' => array(
						array(
							'type' => 'dropdown',								
							'heading' => 'Portfolio List Template',
							'param_name' => 'type',
							'value' => array(
								'Standard' => 'standard',
								'Gallery' => 'gallery',
								'Gallery With Space' => 'gallery-space'
							),
							'admin_label' => true,
							'description' => ''
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Title Tag',
							'param_name' => 'title_tag',
							'value' => array(
								''   => '',
								'h2' => 'h2',
								'h3' => 'h3',
								'h4' => 'h4',
								'h5' => 'h5',
								'h6' => 'h6',
							),
							'admin_label' => true,
							'description' => ''
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Image Proportions',
							'param_name' => 'image_size',
							'value' => array(
								'Original' => 'full',
								'Square' => 'square',
								'Landscape' => 'landscape',
								'Portrait' => 'portrait'
							),
							'save_always' => true,
							'admin_label' => true,
							'description' => ''
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Show Load More',
							'param_name' => 'show_load_more',
							'value' => array(
								'Yes' => 'yes',
								'No' => 'no'
							),
							'save_always' => true,
							'admin_label' => true,
							'description' => 'Default value is Yes'
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Order By',
							'param_name' => 'order_by',
							'value' => array(
								'Menu Order' => 'menu_order',
								'Title' => 'title',
								'Date' => 'date'
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => '',
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Order',
							'param_name' => 'order',
							'value' => array(
								'ASC' => 'ASC',
								'DESC' => 'DESC',
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => '',
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'textfield',
							'heading' => 'One-Category Portfolio List',
							'param_name' => 'category',
							'value' => '',
							'admin_label' => true,
							'description' => 'Enter one category slug (leave empty for showing all categories)',
							'group' => 'Query and Layout Options'
						),
						 array(
							'type' => 'textfield',
							'heading' => 'Number of Portfolios Per Page',
							'param_name' => 'number',
							'value' => '-1',
							'admin_label' => true,
							'description' => '(enter -1 to show all)',
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Number of Columns',
							'param_name' => 'columns',
							'value' => array(
								'' => '',
								'One' => 'one',
								'Two' => 'two',
								'Three' => 'three',
								'Four' => 'four',
								'Five' => 'five',
								'Six' => 'six'
							),
							'admin_label' => true,
							'description' => 'Default value is Three',
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'textfield',
							'heading' => 'Show Only Projects with Listed IDs',
							'param_name' => 'selected_projects',
							'value' => '',
							'admin_label' => true,
							'description' => 'Delimit ID numbers by comma (leave empty for all)',
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Enable Category Filter',
							'param_name' => 'filter',
							'value' => array(
								'No' => 'no',
								'Yes' => 'yes'                               
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => 'Default value is No',
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Enable Icons',
							'param_name' => 'icons',
							'value' => array(
								'Yes' => 'yes',  
								'No' => 'no'
								                             
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => 'Default value is No',
							'dependency' => array('element' => 'type', 'value' => array('standard')),
							'group' => 'Query and Layout Options'
						),
						array(
							'type' => 'dropdown',
							'heading' => 'Filter Order By',
							'param_name' => 'filter_order_by',
							'value' => array(
								'Name'  => 'name',
								'Count' => 'count',
								'Id'    => 'id',
								'Slug'  => 'slug'
							),
							'admin_label' => true,
							'save_always' => true,
							'description' => 'Default value is Name',
							'dependency' => array('element' => 'filter', 'value' => array('yes')),
							'group' => 'Query and Layout Options'
						)
						)
				)
			);
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
            'type' => 'standard',
            'columns' => 'three',
            'image_size' => 'full',
            'order_by' => 'date',
            'order' => 'ASC',
            'number' => '-1',
            'filter' => 'no',
			'icons' => 'yes',
            'filter_order_by' => 'name',
            'category' => '',
            'selected_projects' => '',
            'show_load_more' => 'yes',
            'title_tag' => 'h5',
			'next_page' => '',
			'portfolio_slider' => '',
			'next_page' => '',
			'portfolios_shown' => ''
        );

		$params = shortcode_atts($args, $atts);
		extract($params);

		$query_array = $this->getQueryArray($params);
		$query_results = new \WP_Query($query_array);
		$params['query_results'] = $query_results;

		$classes = $this->getPortfolioClasses($params);
		$data_atts = $this->getDataAtts($params);
		$data_atts .= 'data-max-num-pages = '.$query_results->max_num_pages;
		$params['icon_html'] = '';

		$html = '';


		$html .= '<div class = "qodef-portfolio-list-holder-outer '.$classes.'" '.$data_atts. '>';

		if($filter == 'yes'){
			$params['filter_categories'] = $this->getFilterCategories($params);
			$html .= qode_core_get_shortcode_module_template_part('portfolio','portfolio-filter', '', $params);
		}

		$html .= '<div class = "qodef-portfolio-list-holder clearfix" >';

		if($query_results->have_posts()):
			while ( $query_results->have_posts() ) : $query_results->the_post();

				$params['current_id'] = get_the_ID();
				$params['thumb_size'] = $this->getImageSize($params);

			if($icons=="yes"){
				$params['icon_html'] = $this->getPortfolioIconsHtml($params);
			}

				$params['category_html'] = $this->getItemCategoriesHtml($params);
				$params['categories'] = $this->getItemCategories($params);
				$params['item_link'] = $this->getItemLink($params);
				$params['item_target'] = $this->getItemTarget($params);

				$html .= qode_core_get_shortcode_module_template_part('portfolio',$type, '', $params);

			endwhile;

			$i = 1;
			$columns_num = 3;
			$columns = $params['columns'];
			switch ($columns):
				case 'one':
					$columns_num = 1;
				break;
				case 'two':
					$columns_num = 2;
				break;
				case 'three':
					$columns_num = 3;
				break;
				case 'four':
					$columns_num = 4;
				break;
				case 'five':
					$columns_num = 5;
				break;
				case 'six':
					$columns_num = 6;
				break;
			endswitch;

			while ($i <= $columns_num) {
				$i++;
				if ($columns != 1 && $type != 'gallery') {
					$html .= "<div class='qodef-filler'></div>\n";
				}
			}
		else: 
			
			$html .= '<p>'. _e( 'Sorry, no posts matched your criteria.','select-core' ) .'</p>';
		
		endif;		
		$html .= '</div>'; //close qodef-portfolio-list-holder
		if($show_load_more == 'yes'){
			$html .= qode_core_get_shortcode_module_template_part('portfolio','load-more-template', '', $params);	
		}
		wp_reset_postdata();
		$html .= '</div>'; // close qodef-portfolio-list-holder-outer
        return $html;
	}
	
	/**
    * Generates portfolio list query attribute array
    *
    * @param $params
    *
    * @return array
    */
	public function getQueryArray($params){
		
		$query_array = array();
		
		$query_array = array(
			'post_type' => 'portfolio-item',
			'orderby' =>$params['order_by'],
			'order' => $params['order'],
			'posts_per_page' => $params['number']
		);
		
		if(!empty($params['category'])){
			$query_array['portfolio-category'] = $params['category'];
		}
		
		$project_ids = null;
		if (!empty($params['selected_projects'])) {
			$project_ids = explode(',', $params['selected_projects']);
			$query_array['post__in'] = $project_ids;
		}
		
		$paged = '';
		if(empty($params['next_page'])) {
            if(get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif(get_query_var('page')) {
                $paged = get_query_var('page');
            }
        }
		
		if(!empty($params['next_page'])){
			$query_array['paged'] = $params['next_page'];
			
		}else{
			$query_array['paged'] = 1;
		}
		
		return $query_array;
	}

	/**
    * Generates portfolio icons html
    *
    * @param $params
    *
    * @return html
    */
	public function getPortfolioIconsHtml($params){
		
		$html ='';
		$id = $params['current_id'];
		$slug_list_ = 'pretty_photo_gallery';
		$custom_portfolio_link = get_post_meta(get_the_ID(), 'portfolio_external_link', true);
		$portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_the_permalink($id);
		$target = $custom_portfolio_link != "" ? '_blank' : '_self';
		
		$featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full'); //original size				
		$large_image = $featured_image_array[0];
				
		$html .= '<div class="qodef-item-icons-holder">';
		
		$html .= '<a class="qodef-portfolio-lightbox" title="' . get_the_title($id) . '" href="' . $large_image . '" data-rel="prettyPhoto[' . $slug_list_ . ']"></a>';
		
		if (function_exists('qode_startit_like_portfolio_list')) {
			$html .= qode_startit_like_portfolio_list($id);
		}
		
		$html .= '<a class="qodef-preview" title="Go to Project" href="' . $portfolio_link . '" target="' . $target . '" data-type="portfolio_list"></a>';
		
		$html .= '</div>';
		
		return $html;
        
	}

	/**
	 * Generates portfolio item link
	 *
	 * @param $params
	 *
	 * @return html
	 */
	public function getItemLink($params){
		$id = $params['current_id'];
		$custom_portfolio_link = get_post_meta(get_the_ID(), 'portfolio_external_link', true);
		$portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_the_permalink($id);
		return $portfolio_link;
	}

	/**
	 * Generates portfolio item target
	 *
	 * @param $params
	 *
	 * @return html
	 */
	public function getItemTarget($params){
		$custom_portfolio_link = get_post_meta(get_the_ID(), 'portfolio_external_link', true);
		$target = $custom_portfolio_link != "" ? '_blank' : '_self';
		return $target;
	}

	/**
    * Generates portfolio classes
    *
    * @param $params
    *
    * @return string
    */
	public function getPortfolioClasses($params){
		$classes = array();
		$type = $params['type'];
		$columns = $params['columns'];
		switch($type):
			case 'standard':
				$classes[] = 'qodef-ptf-standard';
			break;
			case 'gallery':
				$classes[] = 'qodef-ptf-gallery';
			break;
			case 'gallery-space':
				$classes[] = 'qodef-ptf-gallery qodef-ptf-gallery-space';
			break;
		endswitch;
		
		if(empty($params['portfolio_slider'])){ // portfolio slider mustn't have this classes
			
			
				switch ($columns):
					case 'one': 
						$classes[] = 'qodef-ptf-one-column';
					break;
					case 'two': 
						$classes[] = 'qodef-ptf-two-columns';
					break;
					case 'three': 
						$classes[] = 'qodef-ptf-three-columns';
					break;
					case 'four': 
						$classes[] = 'qodef-ptf-four-columns';
					break;
					case 'five': 
						$classes[] = 'qodef-ptf-five-columns';
					break;
					case 'six': 
						$classes[] = 'qodef-ptf-six-columns';
					break;
				endswitch;
			
			if($params['show_load_more']== 'yes'){ 
				$classes[] = 'qodef-ptf-load-more'; 
			}
			
		}

		if($params['filter'] == 'yes'){
			$classes[] = 'qodef-ptf-has-filter';
		}
		
		if(!empty($params['portfolio_slider']) && $params['portfolio_slider'] == 'yes'){
			$classes[] = 'qodef-portfolio-slider-holder';
		}
		
		return implode(' ',$classes);
        
	}
	/**
    * Generates portfolio image size
    *
    * @param $params
    *
    * @return string
    */
	public function getImageSize($params){
		
		$thumb_size = 'full';
		$type = $params['type'];
		

		if(!empty($params['image_size'])){
			$image_size = $params['image_size'];

			switch ($image_size) {
				case 'landscape':
					$thumb_size = 'qode_startit_landscape';
					break;
				case 'portrait':
					$thumb_size = 'qode_startit_portrait';
					break;
				case 'square':
					$thumb_size = 'qode_startit_square';
					break;
				case 'full':
					$thumb_size = 'full';
					break;
			}
		}
		
		
		
		return $thumb_size;
	}
	/**
    * Generates portfolio item categories ids.This function is used for filtering
    *
    * @param $params
    *
    * @return array
    */
	public function getItemCategories($params){
		$id = $params['current_id'];
		$category_return_array = array();
		
		$categories = wp_get_post_terms($id, 'portfolio-category');
		
		foreach($categories as $cat){
			$category_return_array[] = 'portfolio_category_'.$cat->term_id;
		}
		return implode(' ', $category_return_array);
	}
	/**
    * Generates portfolio item categories html based on id
    *
    * @param $params
    *
    * @return html
    */
	public function getItemCategoriesHtml($params){
		$id = $params['current_id'];
		
		$categories = wp_get_post_terms($id, 'portfolio-category');
		$category_html = '<div class="qodef-ptf-category-holder">';
		$k = 1;
		foreach ($categories as $cat) {
			$category_html .= '<span>'.$cat->name.'</span>';
			if (count($categories) != $k) {
				$category_html .= ' <span>/</span> ';
			}
			$k++;
		}
		$category_html .= '</div>'; 
		return $category_html;
	}


	/**
    * Generates filter categories array
    *
    * @param $params
    *
    
	 * 
	 * 
	 * 
	 * * @return array
    */
	public function getFilterCategories($params){
		
		$cat_id = 0;
		$top_category = '';
		
		if(!empty($params['category'])){	
			
			$top_category = get_term_by('slug', $params['category'], 'portfolio-category');
			if(isset($top_category->term_id)){
				$cat_id = $top_category->term_id;
			}
			
		}
		
		$args = array(
			'child_of' => $cat_id,
			'order_by' => $params['filter_order_by']
		);
		
		$filter_categories = get_terms('portfolio-category',$args);
		
		return $filter_categories;
		
	}
	/**
    * Generates datta attributes array
    *
    * @param $params
    *
    * @return array
    */
	public function getDataAtts($params){
		
		$data_attr = array();
		$data_return_string = '';
		
		if(get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif(get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
		
		if(!empty($paged)) {
            $data_attr['data-next-page'] = $paged+1;
        }		
		if(!empty($params['type'])){
			$data_attr['data-type'] = $params['type'];
		}
		if(!empty($params['columns'])){
			$data_attr['data-columns'] = $params['columns'];
		}

		if(!empty($params['order_by'])){
			$data_attr['data-order-by'] = $params['order_by'];
		}
		if(!empty($params['order'])){
			$data_attr['data-order'] = $params['order'];
		}
		if(!empty($params['number'])){
			$data_attr['data-number'] = $params['number'];
		}
		if(!empty($params['filter'])){
			$data_attr['data-filter'] = $params['filter'];
		}
		if(!empty($params['filter_order_by'])){
			$data_attr['data-filter-order-by'] = $params['filter_order_by'];
		}
		if(!empty($params['category'])){
			$data_attr['data-category'] = $params['category'];
		}
		if(!empty($params['selected_projectes'])){
			$data_attr['data-selected-projects'] = $params['selected_projectes'];
		}
		if(!empty($params['show_load_more'])){
			$data_attr['data-show-load-more'] = $params['show_load_more'];
		}
		if(!empty($params['title_tag'])){
			$data_attr['data-title-tag'] = $params['title_tag'];
		}
		if(!empty($params['portfolio_slider']) && $params['portfolio_slider']=='yes'){
			$data_attr['data-items'] = $params['portfolios_shown'];
		}
		
		foreach($data_attr as $key => $value) {
			if($key !== '') {
				$data_return_string .= $key.'= '.esc_attr($value).' ';
			}
		}
		return $data_return_string;
	}
}