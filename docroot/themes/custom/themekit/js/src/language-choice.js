"use strict";

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

    //window.console.log(window.location.href.split('?')[1]);
    let query = getQueryVariable('sitechoice');

    if (query) {
      if (query === 'ca') {
        setCookie('sitechoice', 'ca');
      } else if (query === 'us') {
        setCookie('sitechoice', 'us');
      }
    }

  }
};
