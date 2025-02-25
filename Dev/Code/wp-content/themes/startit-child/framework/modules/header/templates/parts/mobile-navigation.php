<?php do_action('qode_startit_before_mobile_navigation'); ?>

<nav class="qodef-mobile-nav">
    <div class="qodef-grid">
        <?php wp_nav_menu(array(
            'theme_location' => 'main-navigation' ,
            'container'  => '',
            'container_class' => '',
            'menu_class' => '',
            'menu_id' => '',
            'fallback_cb' => 'top_navigation_fallback',
            'link_before' => '<span>',
            'link_after' => '</span>',
            'walker' => new QodeStartitMobileNavigationWalker()
        )); ?>
        <div class="mobile-register"><a class="a-register" target="_blank" href="https://eaid.bizzsecure.com/main/company/sign-up">New User SignUp</a></div>
		<button class="menu_trigger">
<svg class="close-icon" width="30" height="30" viewBox="0 0 30 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M16.326 15l13.4-13.4c.366-.366.366-.96 0-1.325-.366-.367-.96-.367-1.326 0L15 13.675 1.6.275C1.235-.093.64-.093.275.275c-.367.365-.367.96 0 1.324l13.4 13.4-13.4 13.4c-.367.364-.367.96 0 1.323.182.184.422.275.662.275.24 0 .48-.09.663-.276l13.4-13.4 13.4 13.4c.183.184.423.275.663.275.24 0 .48-.09.662-.276.367-.365.367-.96 0-1.324L16.325 15z" fill-rule="evenodd"></path>
</svg>
</button>
    </div>
</nav>


<?php do_action('qode_startit_after_mobile_navigation'); ?>

<script>
jQuery(".qodef-mobile-menu-opener").click(function(){
            jQuery('html').addClass('overlay_is_open');
        });
            jQuery(".menu_trigger").click(function(){
        jQuery('html').removeClass('overlay_is_open');
    });
</script>