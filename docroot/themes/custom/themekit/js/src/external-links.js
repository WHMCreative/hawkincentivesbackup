/**
 * @file external-links.js
 *
 * Add target=_blank to all external links.
 */
import $ from 'jquery'; // eslint-disable-line import/no-extraneous-dependencies

Drupal.behaviors.externalLink = {
  attach(context) {
    const englishUrl = drupalSettings.language.domains['en'];
    const canadianUrl = drupalSettings.language.domains['en-ca'];
    const a = new RegExp(`/${window.location.host}/`);
    // Add target="_blank" to all external links.
    $('a', context).each(function () {
      if (!a.test(this.href) && this.href !== '' && !this.href.includes(englishUrl) && !this.href.includes(canadianUrl)) {
        $(this).attr('target', '_blank');
      }
    });

    // Add target="_blank" to all files.
    $('.file > a').each(function () {
      $(this).attr('target', '_blank');
    });
  },
};
