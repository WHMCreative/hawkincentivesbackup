/**
 * @file
 * Magnific Popup
 */
import $ from 'jquery';
import { magnificPopup } from "magnific-popup/dist/jquery.magnific-popup";

Drupal.behaviors.magnificPopup = {
  attach: function (context, settings) {

    let $link = $('a.marketo-modal-cta-link', context);
    if(!$link.length) return;

    $link.on('click', function(e) {
      let $parent_paragraph = $(this).parents('.paragraph--type--link-form-modal'),
        $modalSrc = $parent_paragraph.find('.paragraph--type--reference-marketo-form');
      if ($modalSrc.length) {
        $.magnificPopup.open({
          items: {
            src: $modalSrc,
            type: 'inline'
          },
          closeBtnInside: false,
        });
      }
      e.preventDefault();
    });
  }
};
