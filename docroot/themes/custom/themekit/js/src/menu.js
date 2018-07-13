/**
 * @file
 * Menu
 */

import $ from 'jquery';

Drupal.behaviors.menuMain = {
  attach: function (context, settings) {
    let $mainMenuItem = $('.menu-level-0 > li.menu-item--expanded', context),
        $mainMenuItemLink = $('.menu-level-0 > li.menu-item--expanded > a', context),
        $menuToggle = $('.region-header .menu-toggle');

    $mainMenuItemLink.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).parent().toggleClass('active');
    });

    // hide dropdown when clicked outside
    $(document).once('document-click').on('click', function (e) {
      let $acitveMenuItem = $('.menu-level-0 > li.active', context);
      if($acitveMenuItem.length && !$acitveMenuItem.has(e.target).length > 0) {
        $acitveMenuItem.removeClass('active');
      }
    });

    $menuToggle.once('menu-toggle').on('click', function (e) {
      $('body').toggleClass('menu-open');
    });

  }
};
