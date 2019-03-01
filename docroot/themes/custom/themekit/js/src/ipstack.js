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
      url: 'http://api.ipstack.com/check?access_key=' + accessKey,
      dataType: 'jsonp',
      success: function (json) {

        let countryCode = json.country_code;
        let cookie = getCookie('sitechoice');
        window.console.log(cookie);
        window.console.log(countryCode);

        if ( cookie === 'ca' && window.location.host !== drupalSettings.language.domains['en-ca']) {
          window.location.replace('//' + drupalSettings.language.domains['en-ca'] + window.location.pathname + window.location.search);
        } else if ( cookie === 'us' && window.location.host !== drupalSettings.language.domains['en'] ) {
          window.location.replace('//' + drupalSettings.language.domains['en'] + window.location.pathname + window.location.search);
        } else if ( !cookie && countryCode === 'CA' && window.location.host !== drupalSettings.language.domains['en-ca']) {
          window.location.replace('//' + drupalSettings.language.domains['en-ca'] + window.location.pathname + window.location.search);
        } else if ( !cookie && countryCode === 'US' && window.location.host !== drupalSettings.language.domains['en'])  {
          window.location.replace('//' + drupalSettings.language.domains['en'] + window.location.pathname + window.location.search);
        }

      }
    });


  }
};
