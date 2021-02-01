<?php /* Template Name: ebook */

get_header();

    // First lets set some arguments for the query:
// Optionally, those could of course go directly into the query,
// especially, if you have no others but post type.

$args_ebooks= array(
	'post_type' => 'ebook',
	'posts_per_page' => -1
	// 'paged' => $paged
    // Several more arguments could go here. Last one without a comma.
);
?>

<div class="qodef-container ebook_bg">  

	<div class="qodef-container-inner ebook_div">
		<h1 class="global_h1"><?php echo the_title();; ?></h1>
		<p class="p_ebook"><?php echo the_field('custom_title'); ?></p>
		<ul class="ul_post_col" id="pagination_simple">

			<?php
			// Query the posts:
			$ebooks_query = new WP_Query($args_ebooks);
			// Loop through the obituaries:
			while ($ebooks_query->have_posts()) : $ebooks_query->the_post();
			?>

				<li>
					<div class="post_one ebooks">
						<div class="video_wrapper"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo  the_post_thumbnail('full'); ?></a>
						</div>
						<div class="text_block">
							<h4 class="nipl_feat_title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php  the_title(); ?></a></h4>
							<!-- <span class="play_btn btn_dowload" data-src='<?php the_field('book_link');?>'>
								// <?php //echo do_shortcode('[popup_anything id="11958"]'); ?>
							</span> -->
						</div>
					</div>
				</li>

				<?php 
				// Reset Post Data
				wp_reset_postdata();
			endwhile;
			?>
		</ul>
	</div>
	<div class="book_contact">
		<div class="qodef-container-inner">
		<div class="ebook_inner_form"><h2 class="heading_homepage">Contact <b>Us</b></h2><?php echo do_shortcode('[contact-form-7 id="616" title="Contact Us"]'); ?></div>
		</div>
	</div>
	

</div>

<script type="text/javascript">

   jQuery(document).ready(function(){
   //      jQuery('#pagination_simple').easyPaginate({
   //      paginateElement: 'li',
   //      elementsPerPage: 2,
   //      effect: 'climb'
   //  });

    // jQuery('#blog_video').easyPaginate({
    //     paginateElement: 'li',
    //     elementsPerPage: 3,
    //     effect: 'climb'
    // });

	jQuery(".post_one .play_btn").on("click", function(){
 		var datas = jQuery(this).attr('data-src');
		$( ".embed-responsive-item" ).attr('src' , datas );
	});

	jQuery(".paoc-popup-close").click(function () {
     jQuery('.embed-responsive-item iframe').attr('src' , '');
    });

 });

</script>

<?php  get_footer(); ?>
