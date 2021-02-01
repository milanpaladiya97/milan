<div class="qodef-carousel-item-holder">
	<?php if ($link !== '') { ?>
	<a href="<?php echo esc_url($link)?>" target="<?php echo esc_attr($target)?>">
	<?php } ?>
        <?php if ($hover_type === 'image-change') { ?>
        <span class="qodef-carousel-item-holder-inner">
        <?php } ?>
                <?php if ($image !== '') { ?>
                    <span class="qodef-carousel-first-image-holder <?php echo esc_attr($hover_class); ?> <?php echo esc_attr($carousel_image_classes); ?>">
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_html($title); ?>">
                    </span>
                <?php } ?>
                <?php if ($hover_image !== '') { ?>
                    <span class="qodef-carousel-second-image-holder <?php echo esc_attr($hover_class); ?> <?php echo esc_attr($carousel_image_classes); ?>">
                        <img src="<?php echo esc_url($hover_image); ?>" alt="<?php echo esc_html($title); ?>">
                    </span>
                <?php } ?>
        <?php if ($hover_type === 'image-change') { ?>
        </span>
        <?php } ?>
	<?php if ($link !== '') { ?>
	</a>
	<?php } ?>
</div>