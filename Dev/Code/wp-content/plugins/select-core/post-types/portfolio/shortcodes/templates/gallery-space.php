
<article class="qodef-portfolio-item mix <?php echo esc_attr($categories)?>" >
	<a class ="qodef-portfolio-link" href="<?php echo esc_url($item_link) ?>" target="<?php echo esc_attr($item_target) ?>"></a>
	<div class = "qodef-item-image-holder">
	<div class="qodef-portfolio-shader"></div>	
	<?php
		echo get_the_post_thumbnail(get_the_ID(),$thumb_size);
	?>				
	</div>
	<div class="qodef-item-text-overlay">
		<div class="qodef-item-text-overlay-inner">
			<div class="qodef-item-text-holder">
				<<?php echo esc_attr($title_tag)?> class="qodef-item-title">
					<?php echo esc_attr(get_the_title()); ?>
				</<?php echo esc_attr($title_tag)?>>	
				<?php 
				echo $category_html;
				?>
			</div>
		</div>	
	</div>
</article>

