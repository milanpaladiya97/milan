<?php do_action('qode_startit_before_page_title'); ?>
<?php if($show_title_area) { ?>

    <div class="qodef-title <?php echo qode_startit_title_classes(); ?>" style="<?php echo esc_attr($title_height); echo esc_attr($title_background_color); echo esc_attr($title_background_image); ?>" data-height="<?php echo esc_attr(intval(preg_replace('/[^0-9]+/', '', $title_height), 10));?>" <?php echo esc_attr($title_background_image_width); ?>>
        <div class="qodef-title-image"><?php if($title_background_image_src != ""){ ?><img src="<?php echo esc_url($title_background_image_src); ?>" alt="&nbsp;" /> <?php } ?></div>
        <div class="qodef-title-holder" <?php qode_startit_inline_style($title_holder_height); ?>>
            <div class="qodef-container clearfix">
                <div class="qodef-container-inner">
                    <div class="qodef-title-subtitle-holder" style="<?php echo esc_attr($title_subtitle_holder_padding); ?>">
                        <div class="qodef-title-subtitle-holder-inner">
                        <?php switch ($type){
                            case 'standard': ?>
                                <?php if(! get_field('custom_title'))
                                { ?>
                                <h1 <?php qode_startit_inline_style($title_color); ?>><span><?php qode_startit_title_text(); ?></span></h1>
                                <?php }
                                else
                                {?>
                                     <h1 class="product_subtitle" <?php qode_startit_inline_style($title_color); ?>><span><?php the_field('custom_title');?></span></h1>
                                   <?php  }?>
                                <?php if($has_subtitle){ ?>
                                    <span class="qodef-subtitle" <?php qode_startit_inline_style($subtitle_color); ?>><span><?php qode_startit_subtitle_text(); ?></span></span>
                                <?php } 

                                if(get_field('sub_title'))
                                    { ?>
                                <h2 class="sub_title_h2" <?php qode_startit_inline_style($title_color); ?>><span><?php the_field('sub_title');  ?></span></h2>
                             
                                <?php }
                                ?>
                                <?php if($enable_breadcrumbs){ ?>
                                    <div class="qodef-breadcrumbs-holder"> <?php qode_startit_custom_breadcrumbs(); ?></div>
                                <?php } ?>
                            <?php break;
                            case 'breadcrumb': ?>
								<div class="qodef-title-breadcrumbs-holder">
									<h1 <?php qode_startit_inline_style($title_color); ?>><span><?php qode_startit_title_text(); ?></span></h1>
									<div class="qodef-breadcrumbs-holder"> <?php qode_startit_custom_breadcrumbs(); ?></div>
								</div>
                            <?php break;
                            }
                            ?>
                           

                            <?php

                            if(is_singular('services') || is_singular('product')  ){
                            ?>

                            <div class="p_top_btn custom_margin_single banner_btn_pop"><?php echo do_shortcode('[popup_anything id="9083"]');?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php if ( is_singular( 'services' ) ) {
?>
<?php $banner_data  =  get_option( 'banner_content_option_name' ); echo $banner_data['banner_data_0']; ?>
<?php }?>

<?php do_action('qode_startit_after_page_title'); ?>