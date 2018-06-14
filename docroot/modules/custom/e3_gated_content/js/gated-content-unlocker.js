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
       * @param values
       *   Submitted values.
       */
      Drupal.behaviors.marketoForms.unlockGatedContent = function (form, marketoConfig, values) {

        // Send the request to unlock content.
        $.ajax({
          method: 'POST',
          url: '/marketo/unlock_gated_content',
          data: {
            unlocked_content: marketoConfig.gatedContent,
          }
        })
          .done(function(data) {

            // Look through processed data and reload the page if content is
            // unlocked.
            if (!!data) {

              // Add the offer type parameter.
              window.location.search += data['query_string'];
            }
          });
      }
    }
  };

})(jQuery, Drupal);