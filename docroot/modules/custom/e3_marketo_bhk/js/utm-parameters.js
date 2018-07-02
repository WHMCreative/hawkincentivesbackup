/**
 * @file
 * Defines the UTM params JavaScript API.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  let utmCookie = {

    /**
     * List of basic UTM parameters.
     */
    utmParams: [
      "utm_source",
      "utm_medium",
      "utm_campaign",
      "utm_term",
      "utm_content"
    ],

    /**
     * Get value for the specified UTM parameter.
     *
     * @param {String} name
     *   Name of the UTM parameter.
     *
     * @returns {string}
     *   Parameter value.
     */
    getParameterByName: function (name) {
      name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

      let regexS = "[\\?&]" + name + "=([^&#]*)";
      let regex = new RegExp(regexS);
      let results = regex.exec(window.location.search);
      if (results == null) {
        return "";
      } else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
      }
    },

    /**
     * Check if UTM params are available in URL.
     *
     * @returns {boolean}
     *   True or False, based on the check result.
     */
    utmPresentInUrl: function () {
      let present = false;

      for (let i = 0; i < this.utmParams.length; i++) {
        let param = this.utmParams[i];
        let value = this.getParameterByName(param);

        if (value !== "" && typeof(value) !== 'undefined') {
          present = true;
        }
      }

      return present;
    },

    /**
     * Write UTM params into the cookie.
     */
    writeUtmCookieFromParams: function () {
      for (let i = 0; i < this.utmParams.length; i++) {
        let param = this.utmParams[i];
        let value = this.getParameterByName(param);
        this.writeCookieOnce(param, value)
      }
    },

    /**
     * Write a cookie once.
     *
     * @param {String} name
     *   Cookie name.
     * @param {String} value
     *   Cookie value.
     */
    writeCookieOnce: function (name, value) {
      let existingValue = $.cookie(name),
        domain = "." + document.domain;

      if (!existingValue) {

        $.cookie(name, value, {
          expires: 365,
          path: '/',
          domain: domain
        });
      }
    },

    /**
     * Write referrer information into the cookie.
     */
    writeReferrerOnce: function () {
      let value = document.referrer;
      if (value === "" || value === undefined) {
        this.writeCookieOnce("referrer", "direct");
      } else {
        this.writeCookieOnce("referrer", value);
      }
    },

    /**
     * Referrer cookie value.
     *
     * @returns {String}
     *   Referrer name.
     */
    referrer: function () {
      return $.cookie("referrer");
    }
  };

  // Initialize the functionality.
  utmCookie.writeReferrerOnce();

  if (utmCookie.utmPresentInUrl()) {
    utmCookie.writeUtmCookieFromParams();
  }

}(jQuery, Drupal, window.drupalSettings));