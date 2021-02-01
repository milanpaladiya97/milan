

<div class="footer_subscribe">
	<div class="fa_container">
		<?php 
					$footer_top_section_options = get_option( 'footer_top_section_option_name' ); // Array of All Options
 					// $footer_top_title_0 = $footer_top_section_options['footer_top_title_0'];
		?>
		<h3><?php echo $footer_top_section_options['footer_top_title_0']; ?> <?php echo do_shortcode('[popup_anything id="8785"]'); ?></h3>
	</div>
</div>

<?php
qode_startit_get_footer();

global $qode_startit_toolbar;
if(isset($qode_startit_toolbar)) include("toolbar.php");


if( 'landingpage' == get_post_type() && $post->ID == '11341' || $post->ID == '11588') { 
  ?>
?>



 <!--Modal: myModal-->

<div id="myModal" class="modal fade custm_video_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
<div class="modal-dialog modal-lg" role="document">



<!--Content-->
<div class="modal-content">

<button type="button" class="close" data-dismiss="modal">&times;</button>
<!--Body-->
<div class="modal-body mb-0 p-0">
<div class="embed-responsive embed-responsive-16by9 z-depth-1-half"><iframe class="embed-responsive-item" src="" allowfullscreen="allowfullscreen"></iframe></div>
</div>
<!--Footer-->
</div>
</div>
<!--/.Content-->

</div>

<?php }?>
<!--Modal: myModal-->

<script>
  jQuery('.carousel-inner').children('.item').eq(0).addClass('first_slider');
    jQuery("#myModal").modal('hide');
 </script>
 <script type="text/javascript" src="https://unpkg.com/aos@2.3.0/dist/aos.js"></script>