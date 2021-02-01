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
    </div>
</nav>

<?php do_action('qode_startit_after_mobile_navigation'); ?>