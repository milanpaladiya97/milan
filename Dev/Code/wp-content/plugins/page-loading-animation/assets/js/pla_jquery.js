
(function ($) {

    jQuery(document).ready(function(){       

        // Onload page
        jQuery(window).on('load',function(){
            jQuery("#pla_loader_custom").fadeOut(1500);
            jQuery("#pla_loader1").fadeOut(1000);
            jQuery("#pla_loader3").fadeOut(1500);
            jQuery("#pla_loader4").fadeOut(1500);
            jQuery("#pla_loader5").fadeOut(1500);
            jQuery("#pla_spinner").fadeOut(1500);
            jQuery("#pla_spinner2").fadeOut(1500);
            jQuery("#pla_spinner3").fadeOut(1500);
            jQuery("#pla_spinner4").fadeOut(1500);
            jQuery("#pla_spinner5").fadeOut(1500);
            jQuery("#pla-sk-cube-grid").fadeOut(1500);
            jQuery(".page-loader11").fadeOut(1500);
        });

    });


    $(document).ready(function() {
        
        setTimeout(function(){
            jQuery('body').addClass('loaded');
        }, 3000);
        
    });    

})(jQuery);