/**
 * @file external-links.js
 *
 * Add target=_blank to all external links.
 */

!function ($) {
  // Always use strict mode to enable better error handling in modern browsers.
  "use strict";

  // Add target="_blank" to all external links.
  $('a').each(function() {
    const a = new RegExp('/' + window.location.host + '/');
    if (!a.test(this.href) && this.href !== '') {
      $(this).attr('target', '_blank');
    }
  });

  // Add target="_blank" to all files.
  $('.file > a').each(function() {
    $(this).attr('target', '_blank');
  });

}(jQuery);