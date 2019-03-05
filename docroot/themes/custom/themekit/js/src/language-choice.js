"use strict";

import $ from 'jquery';

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getQueryVariable(variable)
{
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if(pair[0] === variable){return pair[1];}
  }
  return(false);
}

Drupal.behaviors.languageChoice = {
  attach: function (context, settings) {

    $('#block-languageswitcher').appendTo('#block-utility');

    $('.language-link').each(function(){
      let href = $(this).attr('href');
      let query = href.indexOf('?') ? '&sitechoice=' + $(this).attr('hreflang') : '?sitechoice=' + $(this).attr('hreflang');
      $(this).attr('href', $(this).attr('href') + query);
    });

    //window.console.log(window.location.href.split('?')[1]);
    let query = getQueryVariable('sitechoice');

    if (query) {
      if (query === 'en-ca') {
        setCookie('sitechoice', 'en-ca');
      } else if (query === 'en') {
        setCookie('sitechoice', 'en');
      }
    }

  }
};
