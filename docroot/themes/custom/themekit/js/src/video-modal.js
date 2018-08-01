/**
 * @file
 * Magnific Popup
 */
import $ from 'jquery';
import { magnificPopup } from "magnific-popup/dist/jquery.magnific-popup";

Drupal.behaviors.videoModal = {
  attach: function (context, settings) {

    // Video Modal
    let $link = $('span.video-modal', context);
    if(!$link.length) return;

    if (!$link.hasClass('wistia_embed')) {
      console.log('test');
      $link.find('a').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        closeBtnInside: false,

        fixedContentPos: false
      });
    }
  }
};
