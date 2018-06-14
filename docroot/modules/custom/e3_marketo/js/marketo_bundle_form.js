/**
 * @file
 * jQuery to provide summary information inside vertical tabs.
 */

(function ($) {

  'use strict';

  /**
   * Provide summary information for vertical tabs.
   */
  Drupal.behaviors.marketoSourceSettings = {
    attach: function (context) {

      // Provide summary during the marketo bundle configuration.
      $('#edit-marketo-source-settings', context).drupalSetSummary(function (context) {
        let vals = [];
        if ($('#edit-remove-source-styles', context).is(':checked')) {
          vals.push(Drupal.t('Marketo Source Styles Removed'));
        }
        else {
          vals.push(Drupal.t('Marketo Source Styles Enabled'));
        }
        return vals.join('<br/>');
      });
    }
  };

})(jQuery);
