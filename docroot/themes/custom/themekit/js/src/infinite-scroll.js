(function ($, Drupal, debounce) {
  "use strict";

  // Cached reference to $(window).
  var $window = $(window);

  // The threshold for how far to the bottom you should reach before reloading.
  var scrollThreshold = 200;

  // The selector for the automatic pager.
  var automaticPagerSelector = '[data-drupal-views-infinite-scroll-pager="automatic"]';

  // The selector for both manual load and automatic pager.
  var pagerSelector = '[data-drupal-views-infinite-scroll-pager]';

  // The selector for the automatic pager.
  var contentWrapperSelector = '[data-drupal-views-infinite-scroll-content-wrapper]';

  // The event and namespace that is bound to window for automatic scrolling.
  var scrollEvent = 'scroll.views_infinite_scroll';

  /**
   * Insert a views infinite scroll view into the document.
   *
   * @param {jQuery} $newView
   *   New content detached from the DOM.
   */
  $.fn.infiniteScrollInsertView = function ($newView) {
    let $views = this,
    viewDomIds = [];

    // Get inserted views
    $views = $views.add($views.find('.view'));

    // Get DOM id for each view
    $views.each(function (i) {
      // Extract the view DOM ID from the view classes.
      let matches = /(js-view-dom-id-\w+)/.exec($(this).attr('class'));
      viewDomIds.push(matches[1]);
    });

    let $newRows = $newView.find(contentWrapperSelector).children();
    let $newPager = $newView.find('.js-pager__items');

    $.each(viewDomIds, function (index, value) {
      let currentViewId = value.replace('js-view-dom-id-', 'views_dom_id:');

      // Get the existing ajaxViews object.
      let view = Drupal.views.instances[currentViewId];

      if(view.settings.view_name === 'news') {

        let displayId = view.settings.view_display_id,
          type = displayId.replace('block_', 'type-'),
          viewClass = 'view-display-id-' + displayId;

        // Remove once so that the exposed form and pager are processed on
        // behavior attach.
        view.$view.removeOnce('ajax-pager');
        view.$exposed_form.removeOnce('exposed-form');
        // Make sure infinite scroll can be reinitialized.
        let $existingPager = view.$view.children(pagerSelector);
        $existingPager.removeOnce('infinite-scroll' + type);

        let $filteredRows = $(),
            $filteredPager = $();

        // Filter content by type
        $newRows.each(function () {
          let $row = $(this);
          if ($row.find('.node--type-news').hasClass(type)) {
            $filteredRows = $filteredRows.add($row);
          }
        });

        // Separate pagers by type
        $newPager.each(function () {
          let $pager = $(this);
          if($pager.parent().hasClass(viewClass)) {
            $filteredPager = $filteredPager.add($pager);
          }
        });


        // Add the new rows to existing view.
        view.$view.children('.view-content').find(contentWrapperSelector).append($filteredRows);
        // Replace the pager link with the new link and ajaxPageState values.
        $existingPager.replaceWith($filteredPager);
      } else {
        // Remove once so that the exposed form and pager are processed on
        // behavior attach.
        view.$view.removeOnce('ajax-pager');
        view.$exposed_form.removeOnce('exposed-form');
        // Make sure infinite scroll can be reinitialized.
        let $existingPager = view.$view.find(pagerSelector);
        $existingPager.removeOnce('infinite-scroll');

        // Add the new rows to existing view.
        view.$view.find(contentWrapperSelector).append($newRows);
        // Replace the pager link with the new link and ajaxPageState values.
        $existingPager.replaceWith($newPager);
      }








      // Run views and VIS behaviors.
      Drupal.attachBehaviors(view.$view[0]);
    });

  };

  /**
   * Handle the automatic paging based on the scroll amount.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Initialize infinite scroll pagers and bind the scroll event.
   * @prop {Drupal~behaviorDetach} detach
   *   During `unload` remove the scroll event binding.
   */
  Drupal.behaviors.views_infinite_scroll_automatic = {
    attach : function(context, settings) {
      $(context).find(automaticPagerSelector).once('infinite-scroll').each(function() {
        var $pager = $(this);
        $pager.addClass('visually-hidden');
        $window.on(scrollEvent, debounce(function() {
          if (window.innerHeight + window.pageYOffset > $pager.offset().top - scrollThreshold) {
            $pager.find('[rel=next]').click();
            $window.off(scrollEvent);
          }
        }, 200));
      });
    },
    detach: function (context, settings, trigger) {
      // In the case where the view is removed from the document, remove it's
      // events. This is important in the case a view being refreshed for a reason
      // other than a scroll. AJAX filters are a good example of the event needing
      // to be destroyed earlier than above.
      if (trigger === 'unload') {
        if ($(context).find(automaticPagerSelector).removeOnce('infinite-scroll').length) {
          $window.off(scrollEvent);
        }
      }
    }
  };

})(jQuery, Drupal, Drupal.debounce);
