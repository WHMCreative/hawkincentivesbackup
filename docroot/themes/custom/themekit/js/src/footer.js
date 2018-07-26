/**
 * @file
 * Footer
 */

import $ from 'jquery';

Drupal.behaviors.footerManipulations = {
  attach: function (context, settings) {
    // Only the last N menu items can be visible
    const footerSecond = $('.menu--footer > ul', context);
    if (!footerSecond.length) return;
    const liCount = footerSecond.find('> li').length;

    footerSecond.find('> li').each((i, elem) => {
      const $this = $(elem);
      const invertedIndexs = [0, 1, 2]; // index from the end -- The last three elements

      if ( (liCount - i - 1) in invertedIndexs ) {
        $this.addClass('visible');
      }
    });
  }

};
