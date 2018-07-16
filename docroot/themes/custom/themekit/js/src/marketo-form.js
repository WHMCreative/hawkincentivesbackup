/**
 * @file
 * Magnific Popup
 */
import $ from 'jquery';

Drupal.behaviors.mktoForm = {
  attach: function (context, settings) {

    // Labels
    window.onload = () => {
      $('.mktoFormRow', context).each((i, el) => {
        if ($(el).find('.marketo-focus-form-item').length) {
          $(el).find('.marketo-form-item').addClass('simple-label');
        }
      });
    };

  }
};
