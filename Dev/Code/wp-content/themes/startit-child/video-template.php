<?php /* Template Name: video */

get_header();

    // First lets set some arguments for the query:
// Optionally, those could of course go directly into the query,
// especially, if you have no others but post type.

$args_company_video = array(
	'post_type' => 'videos',
	'posts_per_page' => -1,
	'meta_key' => 'video_categories',
	'meta_value'	=> 'Company Videos'
	// 'paged' => $paged
    // Several more arguments could go here. Last one without a comma.
);
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

<div class="video_title">
	<h2>Videos</h2>
</div>

<div class="qodef-container video_bg">

	<?php 
		// Query the posts:
		$company_query = new WP_Query($args_company_video);
		if ($company_query->have_posts()){
	?>
	<div class="qodef-container-inner company_blogs">
		
		<h1 class="global_h1">Company Videos</h1>
		<ul class="ul_post_col">

			<?php
			// Loop through the obituaries:
			while ($company_query->have_posts()) : $company_query->the_post();
			?>
				<li>
					<div class="post_one">
						<div class="video_wrapper"><img src="<?php the_field('preview_thumbnail'); ?>" />
							<span class="play_btn" data-src='<?php the_field('youtube_link');?>'>
								<?php if(get_field('youtube_link'))
								{ ?>
								<i class="fa fa-play-circle fa_play" aria-hidden="true"></i>
								<?php }?>
							</span>
						</div>
						<h4 class="nipl_feat_title"><?php  the_title(); ?></h4>
					</div>
				</li>

				<?php 
		// Reset Post Data
		wp_reset_postdata();
		endwhile;
		?>
		</ul>
		<?php } ?>
	</div>

	<?php 

	// Get Video Blogs Category
	$args_video_blog = array(
		'post_type' => 'videos',
		'posts_per_page' => 5,
		'meta_key' => 'video_categories',
		'meta_value'	=> 'Video Blogs'
    // Several more arguments could go here. Last one without a comma.
	);

	$blog_query = new WP_Query($args_video_blog);
	if ($blog_query->have_posts()){
	?>
	<div class="qodef-container-inner video_blogs">
		
		<h1 class="global_h1">Video Blogs</h1>
		<!-- <ul class="ul_post_col" id="blog_video"> -->
			<ul class="ul_post_col">

			<?php
			// Loop through the obituaries:
			while ($blog_query->have_posts()) : $blog_query->the_post();
			?>
				<li>
					<div class="post_one">
						<div class="video_wrapper"><img src="<?php the_field('preview_thumbnail'); ?>" />
							<span class="play_btn" data-src='<?php the_field('youtube_link');?>'>
								<?php if(get_field('youtube_link'))
								{ ?>
								<i class="fa fa-play-circle fa_play" aria-hidden="true"></i>
								<?php }?>
							</span>
						</div>
						<h4 class="nipl_feat_title"><?php  the_title(); ?></h4>
					</div>
				</li>

			<?php 
			// Reset Post Data
			wp_reset_postdata();
			endwhile;
			?>
		</ul>
		<?php } ?>
	</div>
</div>



<script type="text/javascript">
   jQuery(document).ready(function(){
        jQuery('#pagination_simple').easyPaginate({
        paginateElement: 'li',
        elementsPerPage: 2,
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