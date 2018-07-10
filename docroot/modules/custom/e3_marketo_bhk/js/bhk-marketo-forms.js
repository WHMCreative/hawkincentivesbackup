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
       * @param {String} dataInstance
       *   Configuration instance index.
       */
      Drupal.behaviors.marketoForms.redirectUser = function (form, marketoConfig, values, dataInstance) {

        let instanceConfig = marketoConfig[dataInstance];

        if (typeof(instanceConfig.redirectPath) !== 'undefined' && instanceConfig.redirectPath ) {
          setTimeout(function() { window.location.replace(instanceConfig.redirectPath) }, 500);
        }
      };
    }
  };

})(jQuery, Drupal);