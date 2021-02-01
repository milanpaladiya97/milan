<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <?php qode_startit_wp_title(); ?>
    <?php
    /**
     * @see qode_startit_header_meta() - hooked with 10
     * @see qode_user_scalable - hooked with 10
     */
    if(is_front_page())
    {
    ?>

    <title> Home - <?php bloginfo('name'); ?> </title>
    <?php } ?>
	<?php do_action('qode_startit_header_meta'); ?>

	<?php wp_head(); ?>

  <!-- google search console start  -->
  <meta name="google-site-verification" content="xRaeN9kMhcyOitDErPsesW3mkTDl2v8NPp9gQK-l0Yo" /> 
  <!-- google search console end  -->
  
<!-- aos animation -->
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/aos@2.3.0/dist/aos.css">
  <script type="text/javascript">
    jQuery( document ).ready(function() 
    {
      AOS.init({
        offset: 500,
        duration: 1200,
        easing: 'ease-in-sine',
        delay: 800
      });
  });
</script>

  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-131482645-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-131482645-1');
</script>


<?php
global $post;

if( 'landingpage' == get_post_type() && $post->ID == '11341' || $post->ID == '11588') { 
  ?>
  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  
<?php }?>
<!-- Minimize Zendchat window on page load jQuery -->
<script>
  var waitForZopim = setInterval(function () {
    if (window.$zopim === undefined || window.$zopim.livechat === undefined) {
        return;
    }
    $zopim(function() {
    $zopim.livechat.window.hide();
  });
    clearInterval(waitForZopim);
}, 100);
 
</script>
  <!-- <link rel="stylesheet" href="https://dev.bizzsecure.com/wp-content/themes/startit-child/style.css"> -->
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css">
  
</head>

<script type="text/javascript"> _linkedin_partner_id = "1271964"; window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || []; window._linkedin_data_partner_ids.push(_linkedin_partner_id); </script><script type="text/javascript"> (function(){var s = document.getElementsByTagName("script")[0]; var b = document.createElement("script"); b.type = "text/javascript";b.async = true; b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js"; s.parentNode.insertBefore(b, s);})(); </script> 
<noscript> <img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=1271964&fmt=gif" /> </noscript>

<body <?php body_class(); ?>>
<?php qode_startit_get_side_area(); ?>

<div class="qodef-wrapper">
    <div class="qodef-wrapper-inner">
        <?php qode_startit_get_header(); ?>

        <?php if(qode_startit_options()->getOptionValue('show_back_button') == "yes") { ?>
            <a id='qodef-back-to-top'  href='#'>
                <span class="qodef-icon-stack">
                     <?php
                        qode_startit_icon_collections()->getBackToTopIcon('font_awesome');
                    ?>
                </span>
            </a>
        <?php } ?>
        <?php qode_startit_get_full_screen_menu(); ?>

        <div class="qodef-content" <?php qode_startit_content_elem_style_attr(); ?>>
 <div class="qodef-content-inner"><script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script>