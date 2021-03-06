"use strict";

import $ from 'jquery';

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
//window.console.log('foo');
Drupal.behaviors.ipstack = {
  attach: function (context, settings) {

    // set endpoint and your access key
    // TODO: swap with client key for launch
    let accessKey = 'e9af2a0a8b852229fce1833b9efc7ac0';

    // if we're logged in, bail out
    if (drupalSettings.user.uid > 0) {
      return;
    }
    // get the API result via jQuery.ajax
    $.ajax({
      url: '//api.ipstack.com/check?access_key=' + accessKey,
      dataType: 'jsonp',
      success: function (json) {

        let countryCode = json.country_code;
        const englishUrl = drupalSettings.language.domains['en'];
        const canadianUrl = drupalSettings.language.domains['en-ca'];
        const frenchUrl = drupalSettings.language.domains['fr'];
        let cookie = getCookie('sitechoice');

        if (!cookie) {
          if (countryCode === 'FR' && window.location.host !== frenchUrl) {
            window.location.replace('//' + frenchUrl + window.location.pathname);
          } else if (countryCode === 'CA' && window.location.host !== canadianUrl) {
            window.location.replace('//' + canadianUrl + window.location.pathname);
          } else if (countryCode === 'US' && window.location.host !== englishUrl) {
            window.location.replace('//' + englishUrl + window.location.pathname.replace('/fr/', '/'));
          }
        }
      }
    });


  }
};
