<?php
/**
 * 'pgaf_post_filter' Shortcode
 * 
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Function to handle the 'pgaf_post_filter' shortcode
 * 
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */
function pgafu_postgrid_filter_shortcode( $atts, $content ) {

	// Shortcode Parameters
	extract(shortcode_atts(array(		
		'cat_id' 				=> '',
		'include_cat_child'		=> 'true',		
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		'grid' 					=> '3',		
		'media_size' 			=> 'large',
		'show_date' 			=> 'true',
		'show_category_name' 	=> 'true',
		'show_author' 			=> 'true',
		'image_height' 			=> '',
		'design'				=> 'design-1',		
		'content_words_limit' 	=> '20',
		'show_read_more'        => 'true',
		'content_tail'			=> '...',
		'cat_limit'				=> 0,
		'cat_order'				=> 'asc',
		'image_fit' 			=> 'true',		
		'cat_orderby'			=> 'name',
		'exclude_cat'			=> array(),
		'show_comments'			=> 'true',
		'show_content' 			=> 'true',
		'all_filter_text'		=> '',
		), $atts, 'pgaf_post_filter'));

	$shortcode_designs	= pgafu_post_grid_filter_designs();
	$unique 			= pgafu_get_unique();
	$limit				= !empty($limit) 					? $limit 						: '15';
	$order 				= ( strtolower($order) == 'asc' ) 	? 'ASC' 						: 'DESC';
	$orderby			= !empty($orderby) 					? $orderby 						: 'date';
	$gridcol			= !empty($grid) 					? $grid 						: '3';
	$design 			= array_key_exists( trim($design)  , $shortcode_designs ) ? $design : 'design-1';	
	$cat_id				= (!empty($cat_id))					? explode(',',$cat_id) 			: '';
	$include_cat_child	= ( $include_cat_child == 'false' ) ? false 						: true;	
	$words_limit 		= !empty( $content_words_limit ) 	? $content_words_limit          : 20;
	$content_tail 		= html_entity_decode($content_tail);
	$show_read_more  	= ( $show_read_more == 'false' )    ? false 						: true;	
	$showAuthor 		= ($show_author == 'false')			? 'false'						: 'true';
	$media_size 		= (!empty($media_size))				? $media_size 	                : 'large'; //thumbnail, medium, large, full	
	$showDate 			= ( $show_date == 'false' ) 		? 'false'						: 'true';
	$showCategory 		= ( $show_category_name == 'false' )? 'false' 						: 'true';
	$image_height 		= (!empty($image_height))           ? $image_height                 : '';
	$height_css 		= ($image_height)                   ? 'height:'.$image_height.'px;' : '';
	$cat_limit			= !empty($cat_limit) 					? $cat_limit 				: 0;
	$cat_order 			= ( strtolower($cat_order) == 'asc' ) 	? 'ASC' 					: 'DESC';
	$cat_orderby		= !empty($cat_orderby) 					? $cat_orderby 				: 'name';
	$exclude_cat 		= !empty($exclude_cat)				? explode(',', $exclude_cat) 	: array();
	$image_fit			= ($image_fit == 'false')			? 0 : 1;
	$show_comments 		= ( $show_comments == 'false' ) 		? 'false'						: 'true';
	$showContent 		= ( $show_content == 'false' ) 		? 'false' 						: 'true';
	$all_filter_text 	= !empty($all_filter_text) ? $all_filter_text : __('All', 'post-grid-and-filter-ultimate');

	// Shortcode file
	// Shortcode file
	$post_design_file_path 	= PGAFU_DIR . '/templates/filter/' . $design . '.php';
	$design_file 			= (file_exists($post_design_file_path)) ? $post_design_file_path : '';
	
	wp_enqueue_script( 'wpos-filterizr-js' );
	wp_enqueue_script( 'pgafu-public-js' );

	// Taking some globals
	global $post;
	$image_fit_class	= ($image_fit) 	? 'pgafu-image-fit' : '';	

	// Getting Terms
	$pgafuterms = get_terms( array(
							'taxonomy' 		=> PGAFU_CAT,
							'hide_empty' 	=> true,
							'fields'		=> 'id=>name',
							'number'		=> $cat_limit,
							'order'			=> $cat_order,
							'orderby'		=> $cat_orderby,
							'include'       => $cat_id,
							'exclude'       => $exclude_cat,
				));

	ob_start();

	// If category is there
	if( !is_wp_error($pgafuterms) && !empty($pgafuterms) ) {

		// Getting ids 
		$logo_cats = array_keys( $pgafuterms );

		// WP Query Parameters
		$query_args = array(
				'post_type' 			=> PGAFU_POST_TYPE,
				'post_status' 			=> array( 'publish' ),
				'posts_per_page'		=> -1,
				'order'          		=> $order,
				'orderby'        		=> $orderby,
				'ignore_sticky_posts'	=> true,
			);

		// Category Parameter
		if( !empty($logo_cats) ) {

			$query_args['tax_query'] = array( 
											array(
												'taxonomy' 			=> PGAFU_CAT, 
												'field' 			=> 'term_id',
												'terms' 			=> $logo_cats,
												'include_children'	=> $include_cat_child,
											));
		}

		// WP Query
		$post_query = new WP_Query($query_args);
		$post_count = $post_query->post_count;
		$count      = 0;

		// 
		if( $post_query->have_posts() ) {	?>

		<div class="pgafu-filter-wrp">
			<ul class="pgafu-filter">
				<li class="pgafu-filtr-cat pgafu-active-filtr" data-filter="all"><a href="javascript:void(0);"><?php echo $all_filter_text; ?></a></li>
				<?php foreach ($pgafuterms as $term_id => $term_name) { ?>
					<li class="pgafu-filtr-cat" data-filter="<?php echo $term_id; ?>"><a href="javascript:void(0);"><?php echo $term_name; ?></a></li>
				<?php } ?>
			</ul>

			<div class="pgafu-filtr-container" id="pgafu-post-filtr-<?php echo $unique; ?>">
				<div class="pgafu-post-grid-main pgafu-post-filter <?php echo 'pgafu-'.$design.' '.$image_fit_class; ?> has-no-animation pgafu-clearfix">

				<?php while ($post_query->have_posts()) : $post_query->the_post();
					$count++;	
					$post_featured_image 	= pgafu_get_post_featured_image( $post->ID, $media_size, true );
					$post_link 		        = pgafu_get_post_link( $post->ID );	
					$postcats 	            = get_the_terms($post->ID, PGAFU_CAT);
					$css_class 				= '';
					$usedcat	            = array();
					$cat_links            	= array();
					$comments 				= get_comments_number( $post->ID );
					$reply					= ($comments <= 1)  ? 'Reply' : 'Replies';
					
					if($postcats) {
					foreach ( $postcats as $term ) {
						$term_link = get_term_link( $term );
						$cat_links[] = '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
					}
					}
					$cate_name = join( " ", $cat_links );

					if( !is_wp_error($postcats) && !empty($postcats) ) {
						foreach ($postcats as $postcat) {
							$usedcat[] = $postcat->term_id;
						}
					}
					$data_category = !empty($usedcat) ? implode(', ',$usedcat) : '1';
					
					if($gridcol == '2') {
						$postgrid = "6";
						} else if($gridcol == '3') {
							$postgrid = "4";
						}  else if($gridcol == '4') {
							$postgrid = "3";
						}  else if($gridcol == '5') {
							$postgrid = "c5";	
						} else if ($gridcol == '1') {
							$postgrid = "12";
						} else {
							$postgrid = "12";
						}
				?>

					<div class="pgafu-medium-<?php echo $postgrid; ?> pgafu-columns filtr-item" data-category="<?php echo $data_category; ?>">
					<?php
						// Include shortcode html file
							if( $design_file ) {
								include( $design_file );
							}	
					?>
					</div>

					<?php endwhile; ?>

				</div>
			</div>
		</div>

		<?php
		} // End of have post

		wp_reset_query(); // reset wp query

		$content .= ob_get_clean();
		return $content;

	} // End of category check
}

// 'logo_filter' Shortcode
add_shortcode('pgaf_post_filter', 'pgafu_postgrid_filter_shortcode');