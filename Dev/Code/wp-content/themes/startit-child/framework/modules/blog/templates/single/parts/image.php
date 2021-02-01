<?php
if ( is_singular( 'services' ) &&  get_field('second_image')) { 
if ( has_post_thumbnail() ) { ?>

	<div class="qodef-post-image cstm_full">

			<?php the_post_thumbnail('full'); ?>
			
				<!-- <img src="<?php //the_field('second_image'); ?>" alt="No Image" class="attachment-full size-full wp-post-image"> -->
				<img src="https://dev.bizzsecure.com/wp-content/uploads/2019/08/10-08-Shield-Prod-All-Med.png" alt="No Image" class="attachment-full size-full wp-post-image">
	</div>
<?php } }
	else
	{

if ( has_post_thumbnail() ) { ?>

	<div class="qodef-post-image">

			<?php the_post_thumbnail('full'); ?>
			
	</div>
<?php } 
	}
?>