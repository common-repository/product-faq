'use strict';

(function ($) {
    // FAQ in Single downloads
    $('.accordion-title').on( 'click', function () {
        $('.accordion-title').removeClass('active');
        $('.accordion-content').slideUp('normal');
        if ($(this).next().is(':hidden') === true) {
            $(this).addClass('active');
            $(this).next().slideDown('normal');
        }
    });
    $('.accordion-content').hide();

})(jQuery);
