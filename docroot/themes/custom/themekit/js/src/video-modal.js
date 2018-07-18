/**
 * @file
 * Magnific Popup
 */
import $ from 'jquery';
import { magnificPopup } from "magnific-popup/dist/jquery.magnific-popup";

Drupal.behaviors.videoModal = {
  attach: function (context, settings) {

    // Video Modal
    let $link = $('a.video-modal', context);
    if(!$link.length) return;

    $link.magnificPopup({
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,

      fixedContentPos: false
    });
  }
};
