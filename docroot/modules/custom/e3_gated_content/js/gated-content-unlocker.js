/**
 * @file
 * Handles Marketo gated content.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.gatedContent = {

    attach: function (context, settings) {

      /**
       * Unlock Gated content.
       *
       * @param {object} form
       *   Marketo Form.
       * @param {object} marketoConfig
       *   Marketo Settings.
       * @param {Array} values
       *   Submitted values.
       * @param {String} dataInstance
       *   Configuration instance index.
       */
      Drupal.behaviors.marketoForms.unlockGatedContent = function (form, marketoConfig, values, dataInstance) {

        let instanceConfig = marketoConfig[dataInstance];

        // Send the request to unlock content.
        $.ajax({
          method: 'POST',
          url: '/marketo/unlock_gated_content',
          data: {
            unlocked_content: instanceConfig.gatedContent,
          }
        })
          .done(function(data) {

            // Look through processed data and reload the page if content is
            // unlocked.
            if (!!data) {
              if (window.location.search.length) {
                // Add the offer type parameter. When several parameters are in the query
                window.location.search += '&' + data['query_string'];
              } else {
                // Add the offer type parameter. When the query is empty
                window.location.search += data['query_string'];
              }
            }
          });
      }
    }
  };

})(jQuery, Drupal);