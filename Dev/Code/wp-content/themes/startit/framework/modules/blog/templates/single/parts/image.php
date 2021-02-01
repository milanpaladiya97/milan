<?php if ( has_post_thumbnail() ) { ?>

	<div class="qodef-post-image cstm_full">

			<?php the_post_thumbnail('full'); ?>
			<?php if( get_field('second_image') ) { ?>
				<img src="<?php the_field('second_image'); ?>" alt="" class="attachment-full size-full wp-post-image">
			<?php }?>
	</div>
<?php } ?>