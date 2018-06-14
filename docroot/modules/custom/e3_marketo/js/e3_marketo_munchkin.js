/**
 * @file
 * Injects Marketo munchkin tracking.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.marketoMunchkin = {

    attach: function (context, settings) {

      /**
       * Init Munchkin tracking.
       */
      const init = function () {

        let config = getMunchkinID();

        // Integrate with the GDPR module if it is installed.
        let cookiesAccepted = true;
        if (typeof GDPR !== 'undefined') {
          cookiesAccepted = GDPR.cookiesAccepted();
        }

        if (cookiesAccepted  && typeof Munchkin !== 'undefined') {
          Munchkin.init(config);
        }
        else if (!cookiesAccepted) {

          let domain = "." + document.domain;
          $.removeCookie('_mkto_trk', { path: '/', domain: domain });
        }
      };

      /**
       * Get Munchkin ID from Marketo Settings.
       */
      const getMunchkinID = function () {
        if (typeof(settings.marketoForms) != 'undefined' &&
          typeof(settings.marketoForms.munchkinId) != 'undefined' &&
          settings.marketoForms.munchkinId) {

          return settings.marketoForms.munchkinId;
        }
        return false;
      };

      // Delay init if GDPR module is installed.
      if (typeof GDPR !== 'undefined' && !GDPR.initialisationComplete) {
        $(window).on("gdpr:load", function() {
          init();
        });
      }
      else {
        init();
      }
    }
  }

})(jQuery, Drupal);