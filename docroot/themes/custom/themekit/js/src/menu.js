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
      let $parent = $(this).parent();
      $parent.siblings().removeClass('active'); // hide open dropdowns
      $parent.toggleClass('active');
    });

    alignMenuDropdown();

    $(window).on('changed.zf.mediaquery', function(event, newSize, oldSize) {
      if (newSize === 'marge' || oldSize === 'marge') {
        alignMenuDropdown();
      }
    });

    // hide dropdown when clicked outside
    $(document).once('document-click').on('click', function (e) {
      let $acitveMenuItem = $('.menu-level-0 > li.active', context),
          $menuMain = $('.menu--main', context);
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


    function alignMenuDropdown() {
      if (Foundation.MediaQuery.atLeast('large')) {
        $mainMenuItemLink.each(function () {
          if ($(this).hasClass('align-second-column')) {
            let $menuLink = $(this),
              $firstItemWidth = $('.menu-level-0 > li:first-child', context).outerWidth(true);
            $menuLink.parent().addClass('static');
            $menuLink.next().addClass('align-second-column').css('left', $firstItemWidth - 5);
          }
        });
      } else {
        let $alignSecondColumnDropdown = $('.menu-level-0 > li .menu-dropdown.align-second-column', context);
        if($alignSecondColumnDropdown.length) {
          $alignSecondColumnDropdown.css('left', '').removeClass('align-second-column');
        }
      }
    }

  }
};
