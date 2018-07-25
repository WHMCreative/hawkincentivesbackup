/**
 * @file
 * Footer
 */

import $ from 'jquery';

Drupal.behaviors.footerManipulations = {
  attach: function (context, settings) {
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

    // Copyright Block
    const copyrightBlock = $('.footer-content #block-copyright', context);

    $(window)
      .on('changed.zf.mediaquery', () => {
        if ($(window).width() < 640) {
          console.log('<640');
          copyrightBlock.appendTo('footer .region-footer-second');
        } else {
          console.log('>640');
          copyrightBlock.appendTo('footer .region-footer-first');
        }
      });

    // Initialization for mobile
    if ($(window).width() < 640) {
      copyrightBlock.appendTo('footer .region-footer-second');
    }
  }

};
