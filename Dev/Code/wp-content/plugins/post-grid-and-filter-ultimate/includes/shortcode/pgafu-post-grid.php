<?php
/**
 * 'pgaf_post_grid' Shortcode
 * 
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Function to handle the 'pgaf_post_grid' shortcode
 * 
 * @package Post grid and filter ultimate
 * @since 1.0.0
 */
function pgafu_post_grid_shortcode( $atts, $content ) {
	
	// Shortcode Parameters
	extract(shortcode_atts(array(
		'limit' 				=> '15',
		'cat_id' 				=> '',
		'include_cat_child'		=> 'true',		
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		'grid' 					=> '3',		
		'image_fit' 			=> 'true',		
		'media_size' 			=> 'large',
		'show_date' 			=> 'true',
		'show_category_name' 	=> 'true',
		'show_author' 			=> 'true',
		'image_height' 			=> '',
		'design'				=> 'design-1',		
		'content_words_limit' 	=> '20',
		'show_read_more'        => 'true',
		'content_tail'			=> '...',
		'pagination' 			=> 'true',
		'pagination_type'		=> 'numeric',
		'show_comments'			=> 'true',
		'show_content' 			=> 'true',
		), $atts, 'pgaf_post_grid'));
	
	$shortcode_designs	= pgafu_post_grid_designs();	
	$limit				= !empty($limit) 					? $limit 						: '15';
	$order 				= ( strtolower($order) == 'asc' ) 	? 'ASC' 						: 'DESC';
	$orderby			= !empty($orderby) 					? $orderby 						: 'date';
	$gridcol			= !empty($grid) 					? $grid 						: '1';
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
	$pagination 		= ($pagination == 'false')			? 'false'						: 'true';
	$pagination_type 	= ($pagination_type == 'prev-next')	? 'prev-next' 					: 'numeric';
	$image_fit			= ($image_fit == 'false')			? 0                             : 1;
	$show_comments 		= ( $show_comments == 'false' ) 		? 'false'					: 'true';
	$showContent 		= ( $show_content == 'false' ) 		? 'false' 						: 'true';
	
	// Shortcode file
	$post_design_file_path 	= PGAFU_DIR . '/templates/grid/' . $design . '.php';
	$design_file 			= (file_exists($post_design_file_path)) ? $post_design_file_path : '';
	
	// Taking some globals
	global $post, $paged;
	$count 			= 0; 
	$image_fit_class	= ($image_fit) 	? 'pgafu-image-fit' : '';
	
	// Pagination parameter
	if(is_home() || is_front_page()) {
		$paged = get_query_var('page');
	} else {
		$paged = get_query_var('paged');
	}
	
	// WP Query Parameters
	$query_args = array(
			'post_type' 			=> PGAFU_POST_TYPE,
			'post_status' 			=> array( 'publish' ),
			'posts_per_page'		=> $limit,
			'order'          		=> $order,
			'orderby'        		=> $orderby,			
			'ignore_sticky_posts'	=> true,
			'paged'         		=> $paged,
		);

	// Category Parameter
	if( !empty($cat_id) ) {
		$query_args['tax_query'] = array( 
										array(
											'taxonomy' 			=> PGAFU_CAT, 
											'field' 			=> 'term_id',
											'terms' 			=> $cat_id,
											'include_children'	=> $include_cat_child,
										));

	} 

	// WP Query
	$post_query = new WP_Query($query_args);
	$post_count = $post_query->post_count;
	
	ob_start();
	
	// If post is there
	if ( $post_query->have_posts() ) {
?>

		<div class="pgafu-post-grid-main <?php echo 'pgafu-'.$design.' '.$image_fit_class; ?> pgafu-grid-<?php echo $gridcol; ?> pgafu-clearfix">			
			<?php
			while ( $post_query->have_posts() ) : $post_query->the_post();				
				$count++;
				$cat_links 				= array();
				$css_class 				= '';
				$post_featured_image 	= pgafu_get_post_featured_image( $post->ID, $media_size, true );
				$post_link 		        = pgafu_get_post_link( $post->ID );				
				$terms 					= get_the_terms( $post->ID, PGAFU_CAT );				
				$comments 				= get_comments_number( $post->ID );
				$reply					= ($comments <= 1)  ? 'Reply' : 'Replies';
				
				if($terms) {
					foreach ( $terms as $term ) {
						$term_link = get_term_link( $term );
						$cat_links[] = '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
					}
				}
				$cate_name = join( " ", $cat_links );
				
				if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == ($count - 1) % $grid ) ) || 1 == $count ) { $css_class .= ' pgafu-first'; }
				if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == $count % $grid ) ) || $post_count == $count ) { $css_class .= ' pgafu-last'; }
	            
	            // Include shortcode html file
				if( $design_file ) {
					include( $design_file );
				}			

			endwhile; ?>
		</div>

		<?php if($pagination == "true") { ?>
			<div class="pgafu-post-pagination pgafu-clearfix">
				<?php if($pagination_type == "numeric") {				
					echo pgafu_pagination( array( 'paged' => $paged , 'total' => $post_query->max_num_pages ) );
				} else { ?>
					<div class="button-post-p"><?php next_posts_link( ' Next >>', $post_query->max_num_pages ); ?></div>
					<div class="button-post-n"><?php previous_posts_link( '<< Previous' ); ?></div>
				<?php } ?>
			</div>
		<?php }
	} // end of have_post()

	wp_reset_query(); // Reset WP Query

    $content .= ob_get_clean();
    return $content;
}
add_shortcode('pgaf_post_grid', 'pgafu_post_grid_shortcode');