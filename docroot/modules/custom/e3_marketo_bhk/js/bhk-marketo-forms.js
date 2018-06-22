/**
 * @file
 * Handles custom BHK Marketo Forms 2.0 features.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.bhkMarketoForms = {

    attach: function (context, settings) {

      /**
       * Redirect user to the specified external/internal URL.
       *
       * @param {object} form
       *   Marketo Form.
       * @param {object} marketoConfig
       *   Marketo Settings.
       * @param values
       *   Submitted values.
       */
      Drupal.behaviors.marketoForms.redirectUser = function (form, marketoConfig, values) {

        if (typeof(marketoConfig.redirectPath) !== 'undefined' && marketoConfig.redirectPath ) {
          setTimeout(function() { window.location.replace(marketoConfig.redirectPath) }, 500);
        }
      };
    }
  };

})(jQuery, Drupal);