(function($) {
  'use strict';

  $('#woo_product_faq_section .inside').sortable({
    items: '> .faq-block',
  });

  $('#woo_product_faq_button').on('click', function(e) {
    e.preventDefault();
    let count = $('.faq-block').length + 1;
    let obj = woo_product_faq_object;
    $('#faq-button').
        before('<div class="faq-block"><div class="faq-title">' + obj.title +
            ': <span class="faq-number">' + count +
            '</span><span class="remove">' + obj.remove +
            '</span></div><input type="text" placeholder="' + obj.input +
            '" name="' + obj.prefix + '[title][]"><textarea placeholder="' +
            obj.textarea + '" rows="5"  name="' + obj.prefix +
            '[desc][]"></textarea></div>');
  });
  $('#woo_product_faq_section').on('click', '.faq-block .remove', function() {
    $(this).closest('.faq-block').remove();
  });

})(jQuery);