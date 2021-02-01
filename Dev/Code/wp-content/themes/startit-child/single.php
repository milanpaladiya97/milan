<?php get_header(); ?>
<?php if (have_posts()) : ?>



<?php while (have_posts()) : the_post(); ?>
<?php get_template_part( 'title' ); ?>
<?php get_template_part('slider'); ?>
	<div class="qodef-container">
		<?php do_action('qode_startit_after_container_open'); ?>
		<div class="qodef-container-inner">
		<?php
	if ( is_singular( 'post' ) || is_singular( 'video' ) ) {
   ?>
 <div class="notice_msg_io notice-blog">  <p class="p-link-vir"><a href="https://calendly.com/vivek-sharma-1/virtual-infosec-team">Schedule a Free 1 hour Consultation with Our Virtual InfoSec Team</a></p></div>
			
<?php }
 ?>
		
			<?php qode_startit_get_blog_single(); ?>
		</div>
		<?php do_action('qode_startit_before_container_close'); ?>
	</div>
<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>