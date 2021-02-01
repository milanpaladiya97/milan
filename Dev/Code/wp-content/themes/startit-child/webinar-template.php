<?php /* Template Name: webinars */

get_header();

    // First lets set some arguments for the query:
// Optionally, those could of course go directly into the query,
// especially, if you have no others but post type.



$args_company_video = array(
	'post_type' => 'webinars',
	'posts_per_page' => -1
	// 'paged' => $paged
    // Several more arguments could go here. Last one without a comma.
);
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

<?php get_template_part( 'title' ); ?>
<div class="qodef-container">

	<div class="qodef-container-inner webinars_div">
		<h1 class="global_h1">Webinars</h1>
		<ul class="ul_post_col ul_cstm_Webnair" id="pagination_simple">

			<?php
// Query the posts:
			$company_query = new WP_Query($args_company_video);

// Loop through the obituaries:
			while ($company_query->have_posts()) : $company_query->the_post();

					 ?>

				<li>
					<div class="post_one webinars">
						<div class="video_wrapper"><img src="<?php the_field('webinar_image'); ?>" />
							
						</div>
						<div class="webnair_cstm">
						<h4 class="nipl_feat_title webinars_title"><?php  the_title(); ?><span><?php the_field('webinars_date');?></span></h4>
						<p><?php  the_content(); ?></p>
						<span class="play_btn" data-src='<?php the_field('webinar_youtube_link');?>'>View</span>
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
	

<script type="text/javascript">
   jQuery(document).ready(function(){
        jQuery('#pagination_simple').easyPaginate({
        paginateElement: 'li',
        elementsPerPage: 5,
        effect: 'climb'
    });

    jQuery('#blog_video').easyPaginate({
        paginateElement: 'li',
        elementsPerPage: 3,
        effect: 'climb'
    });

	jQuery(".post_one .play_btn").on("click", function(){
 var datas = jQuery(this).attr('data-src');

$( ".embed-responsive" ).append( datas );
 jQuery("#myModal").modal('show');
});

	   jQuery(".paoc-popup-close").click(function () {
  
     jQuery('.embed-responsive-item iframe').remove();
    
    });
 });

</script>



<?php 
get_footer();

?>


<div id="myModal" class="modal fade custm_video_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body mb-0 p-0">
				<div class="embed-responsive embed-responsive-16by9 z-depth-1-half"></div>
			</div>
		</div>
	</div>

</div>
