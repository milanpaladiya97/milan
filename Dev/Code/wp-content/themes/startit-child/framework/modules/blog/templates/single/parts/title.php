<?php

	if(isset($title_tag)){

		$title_tag = $title_tag;

	}else{

		$title_tag = 'h2';

	}

if(!is_singular('services')){
?>

<<?php echo esc_attr($title_tag);?> class="qodef-post-title">

	<?php the_title(); ?>

</<?php echo esc_attr($title_tag); ?>>

<?php } ?>