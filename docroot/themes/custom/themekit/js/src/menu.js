/**
 * @file
 * Menu
 */

import $ from 'jquery';
import { magnificPopup } from "magnific-popup/dist/jquery.magnific-popup";

Drupal.behaviors.menuMain = {
  attach: function (context, settings) {
    let $header = $('.region-header', context);
    if(!$header.length) return;

    let $mainMenuItem = $header.find('.menu-level-0 > li.menu-item--expanded'),
        $mainMenuItemLink = $header.find('.menu-level-0 > li.menu-item--expanded > a'),
        $menuToggle = $header.find('.menu-toggle');

    $mainMenuItemLink.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      let $parent = $(this).parent(),
          $siblings = $parent.siblings('.active');
      if($siblings.length) {
        $siblings.removeClass('active'); // hide open dropdown
      }
      $parent.toggleClass('active');
    });

    // hide dropdown when clicked outside
    $(document).once('document-click').on('click', function (e) {
      let $acitveMenuItem = $header.find('.menu-level-0 > li.active'),
          $menuMain = $header.find('.menu--main');
      if($acitveMenuItem.length && !$acitveMenuItem.has(e.target).length > 0) {
        $acitveMenuItem.removeClass('active');
      }

      // handling mobile off-canvas menu
      if($menuMain.length && !$menuMain.has(e.target).length > 0) {
        let targetClassList = e.target.classList;
        if (!~$.inArray('menu-toggle', targetClassList) && !~$.inArray('menu--main', targetClassList)) {
          $('body').removeClass('menu-open');
        }
      }

    });

    $menuToggle.once('menu-toggle').on('click', function (e) {
      $('body').toggleClass('menu-open');
    });

  }

};
