(function($) {
    'use strict';

    $(document).ready(function () {
        qodefInitQuantityButtons();
        qodefInitSelect2();
        qodeInitSingleProductLightbox();
    });

    function qodefInitQuantityButtons() {

        $(document).on( 'click', '.qodef-quantity-minus, .qodef-quantity-plus', function(e) {    
            e.stopPropagation();

            var button = $(this),
                inputField = button.siblings('.qodef-quantity-input'),
                step = parseFloat(inputField.attr('step')),
                max = parseFloat(inputField.attr('max')),
                minus = false,
                inputValue = parseFloat(inputField.val()),
                newInputValue;

            if (button.hasClass('qodef-quantity-minus')) {
                minus = true;
            }

            if (minus) {
                newInputValue = inputValue - step;
                if (newInputValue >= 1) {
                    inputField.val(newInputValue);
                } else {
                    inputField.val(1);
                }
            } else {
                newInputValue = inputValue + step;
                if ( max === undefined ) {
                    inputField.val(newInputValue);
                } else {
                    if ( newInputValue >= max ) {
                        inputField.val(max);
                    } else {
                        inputField.val(newInputValue);
                    }
                }
            }

            inputField.trigger( 'change' );
        });
    }

    function qodefInitSelect2() {

        if ($('.woocommerce-ordering .orderby').length ||  $('#calc_shipping_country').length ) {

            $('.woocommerce-ordering .orderby').select2({
                minimumResultsForSearch: Infinity
            });

            $('#calc_shipping_country').select2();

        }

        if($('table.variations').length > 0) {
            $('table.variations').find('td.value').each(function() {
                $(this).find('select').select2({
                    minimumResultsForSearch: -1
                }).on("select2-opening", function() { $(this).trigger('focusin'); });
            });
        }
    }

    /*
     ** Init Product Single Pretty Photo attributes
     */
    function qodeInitSingleProductLightbox() {
        "use strict";

        var item = $('.woocommerce.single-product .product .images .woocommerce-product-gallery__image');

        if(item.length) {
            item.each(function() {
                var thisItem = $(this).children('a');

                thisItem.attr('data-rel', 'prettyPhoto[woo_single_pretty_photo]');

                $('a[data-rel]').each(function() {
                    $(this).attr('rel', $(this).data('rel'));
                });

                $("a[rel^='prettyPhoto']").prettyPhoto({
                    animation_speed: 'normal', /* fast/slow/normal */
                    slideshow: false, /* false OR interval time in ms */
                    autoplay_slideshow: false, /* true/false */
                    opacity: 0.80, /* Value between 0 and 1 */
                    show_title: true, /* true/false */
                    allow_resize: true, /* Resize the photos bigger than viewport. true/false */
                    horizontal_padding: 0,
                    default_width: 650,
                    default_height: 400,
                    counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
                    theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
                    hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
                    wmode: 'opaque', /* Set the flash wmode attribute */
                    autoplay: true, /* Automatically start videos: True/False */
                    modal: false, /* If set to true, only the close button will close the window */
                    overlay_gallery: false, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
                    keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
                    deeplinking: false,
                    social_tools: false
                });
            });
        }
    }

})(jQuery);