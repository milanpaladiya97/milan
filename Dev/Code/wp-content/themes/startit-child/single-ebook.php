<?php get_header();  global $post; ?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

<div class="ebook_banner">
	<div class="qodef-container-inner">
		<div class="ebook_banner_inner">
			<div class="ebook_banner_l">
				<h2><?php the_title(); ?></h2>
				<?php the_excerpt(); ?>
				<div class="btn_ebook"><a href="#">Download</a></div>
			</div>
			<div class="ebook_banner_r">
				<div class="ebook_img"><?php echo  the_post_thumbnail('full'); ?></div>
			</div>
		</div>
	</div>
</div>

<div class="ebbok_text_Wrapper">
	<div class="qodef-container-inner">
		<div class="ebbok_center_content">
			<div class="ebook_center_l"><?php the_content(); ?></div>
			<div class="ebook_center_r"><img src="<?php echo the_field('book_image' , $post->ID);?>"/></div>
		</div>
	</div>
	<div id="download" ></div>
</div>

<div class="ebook_detail" >
	<div class="qodef-container-inner">
		<div class="ebbok_detail_inner">
			<div class="ebook_form"><h2>PLEASE FILL FORM TO DOWNLOAD eBOOK</h2><?php echo do_shortcode('[contact-form-7 id="11957" title="eBook Download"]'); ?></div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery( document ).ready(function() {
	   	jQuery(".paoc-popup-close").click(function () {
     		jQuery('.embed-responsive-item iframe').remove();
    	});


    jQuery(".btn_ebook a").click(function() {
    	jQuery('html,body').animate({
        	scrollTop: jQuery("#download").offset().top},
        'slow');
	});
});
</script>



<?php get_footer(); ?>

<div id="myModal" class="modal fade custm_video_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body mb-0 p-0">
				<div class="embed-responsive embed-responsive-16by9 z-depth-1-half"> <iframe class="embed-responsive-item"  src="<?php echo the_field('book_link' , $post->ID);?>" allowfullscreen="allowfullscreen"></iframe> </div>
			</div>
		</div>
	</div>

</div>