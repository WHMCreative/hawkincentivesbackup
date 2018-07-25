/**
 * @file
 * Slick
 */
import $ from 'jquery';
import { Slick } from "slick-carousel/slick/slick";

Drupal.behaviors.slickCustom = {
  attach: function (context, settings) {
    const tabContainerSelector = '.paragraph--type--compound-tabbed-content .field--name-field-p-tab-content';
    const tabNavSelector = '.slick-tab-nav';
    const tabContainer = $(tabContainerSelector, context);
    const tabLabelSelector = '.field--name-field-label';

    if (!tabContainer.length) return;

    /**
     * Do some things for the container.
     */
    const manipulateContainer = (container, containerUniqueClass) => {
      const r = $.Deferred();

      container.addClass(containerUniqueClass);

      return r;
    };

    /**
     * Build navigation using labels.
     *
     * @param {Object} container
     *   Tab container.
     * @param {String} containerIndex
     *   Tab container index.
     * @param {String} labelSelector
     *   Label selector.
     *
     */
    const buildNav = (container, containerIndex, labelSelector) => {
      const r = $.Deferred();
      const labels = container.find(labelSelector);
      let tabNavElement = '';

      labels.each((i, el) => {
        tabNavElement += `<a href="#" class="slick-tab-nav--element">${$(el).text()}</a>`;
      });
      container.after(`<div class="slick-tab-nav slick-tab-nav-${containerIndex}">${tabNavElement}</div>`);

      return r;
    };

    /**
     * Build navigation with counts.
     *
     * @param {Object} container
     *   Tab container.
     * @param {String} navSelector
     *   Navigation selector.
     * @param {String} labelSelector
     *   Label selector.
     *
     */
    const buildNavCount = (container, navSelector, labelSelector) => {
      let tabNavElementNumbers = '',
          tabNavElementLabels = '',
          currentContainer = container,
          labels = currentContainer.find(labelSelector),
          currentSlideIndex = currentContainer.find('.slick-current').index(),
          navContainer = currentContainer.next(navSelector);

      if (!navContainer.find('.slick-counter-numbers').length &&
          !navContainer.find('.slick-counter-labels').length)
      {
        labels.each((i, el) => {
          tabNavElementNumbers += `<span>${i + 1}/${labels.length}</span>`;
          tabNavElementLabels += `<span>${$(el).text()}</span>`;
        });

        navContainer.prepend(`<div class="slick-counter-numbers">${tabNavElementNumbers}</div>`);
        navContainer.prepend(`<div class="slick-counter-labels">${tabNavElementLabels}</div>`);
      }

      // Init
      navContainer.find('.slick-counter-numbers span').removeClass('active');
      navContainer.find('.slick-counter-labels span').removeClass('active');

      navContainer.find('.slick-counter-numbers span').eq(currentSlideIndex).addClass('active');
      navContainer.find('.slick-counter-labels span').eq(currentSlideIndex).addClass('active');
    };

    /**
     * Init slick.
     *
     * @param {String} containerSelector
     *   Tab container selector.
     * @param {String} indexContainer
     *   Tab container index.
     * @param {String} uniqueContainerSelector
     *   Tab container unique selector.
     * @param {String} navSelector
     *   Navigation selector.
     * @param {String} labelSelector
     *   Label selector.
     *
     */
    const buildSlick = (containerSelector, indexContainer, uniqueContainerSelector, navSelector, labelSelector) => {
      const slidesCount = $(containerSelector).eq(indexContainer).find(labelSelector).length;

      $(containerSelector).eq(indexContainer).slick({
        slidesToShow: 1,
        arrows: false,
        fade: true,
        adaptiveHeight: true,
        asNavFor: `${navSelector}-${indexContainer}`,
        responsive: [
          {
            breakpoint: 800,
            settings: {
              dots: true,
            }
          },
        ]
      });

      $(`${navSelector}-${indexContainer}`).slick({
        slidesToShow: slidesCount,
        asNavFor: `.${uniqueContainerSelector}`,
        dots: false,
        centerMode: true,
        focusOnSelect: true,
      });

      // Set tabindex to 0 after slick init
      $(`${navSelector}--element`).each((i, el) => {
        $(el).attr('tabindex', '0');
      });
    };

    tabContainer.each((indexTabContainer, elemTabContainer) => {
      let elementTC = $(elemTabContainer);
      const uniqueClass = `tabbed-content-${indexTabContainer}`;

      // Now call the functions one after the other
      manipulateContainer(elementTC, uniqueClass).done(
        buildNav(elementTC, indexTabContainer, tabLabelSelector).done(
          buildSlick(tabContainerSelector, indexTabContainer, uniqueClass, tabNavSelector, tabLabelSelector)
        )
      );
    });

    $(window).on('load resize orientationchange', () => {
      if ($(window).width() <= 800) {
        tabContainer.each((indexTabContainer, elemTabContainer) => {
          let elementTC = $(elemTabContainer);

          buildNavCount(elementTC, tabNavSelector, tabLabelSelector);
        });
      }
    });

    $(document).on('click', '.slick-tab-nav--element', (event) => {
      event.preventDefault();

      // Set tabindex to 0 after click
      $('.slick-tab-nav--element').each((i, el) => {
        $(el).attr('tabindex', '0');
      });
    });

    $(document).on('touchend', '.slick-list.draggable', (event) => {
      const $this = $(event.currentTarget);
      const index = $this.find('.slick-slide.slick-active').index();
      const myContainer = $this.closest('.paragraph');

      // Update slick counter numbers class
      myContainer.find(`.slick-counter-numbers span`).removeClass('active');
      myContainer.find(`.slick-counter-numbers span:nth-of-type(${index + 1})`).addClass('active');

      // Update slick counter labels class
      myContainer.find(`.slick-counter-labels span`).removeClass('active');
      myContainer.find(`.slick-counter-labels span:nth-of-type(${index + 1})`).addClass('active');
    });

    $(document).on('click', '.slick-dots button', (event) => {
      const $this = $(event.currentTarget);
      const index = $this.parent().index();
      const myContainer = $this.closest('.paragraph');

      // Update slick counter numbers class
      myContainer.find(`.slick-counter-numbers span`).removeClass('active');
      myContainer.find(`.slick-counter-numbers span:nth-of-type(${index + 1})`).addClass('active');

      // Update slick counter labels class
      myContainer.find(`.slick-counter-labels span`).removeClass('active');
      myContainer.find(`.slick-counter-labels span:nth-of-type(${index + 1})`).addClass('active');
    });

  }
};

Drupal.behaviors.mediaTile = {
  attach: function (context, settings) {
    let $content = $('.paragraph--type--compound-media-title-bar', context);
    if(!$content.length) return;

    $content.each(function () {

      let $this = $(this),
        $slides = $this.find('.field--name-field-p-media-tile-content');

      $this.on('init reInit afterChange', function(event, slick, currentSlide, nextSlide) {
        let $count = $this.find('.slick-counter'),
          i = currentSlide ? currentSlide : 0,
          counterElements = '';

        if (!$count.length) {
          for (let n = 1; n <= slick.slideCount; n++) {
            if (n === 1) {
              counterElements += `<span class="active">${n}/${slick.slideCount}</span>`;
            } else
            counterElements += `<span>${n}/${slick.slideCount}</span>`;
          }
          $this.find('> .paragraph-content').append('<div class="slick-counter">' + counterElements + '</div>');
          $count.find('span').eq(i).addClass('active');
        }

        $count.find('span').removeClass('active');
        $count.find('span').eq(i).addClass('active');
      });

      $(window).on('load resize orientationchange', function() {
        // slick on mobile
        if ($(window).width() > 800) {
          if ($slides.hasClass('slick-initialized')) {
            $slides.slick('unslick');
          }
        }
        else{
          if (!$slides.hasClass('slick-initialized')) {
            $slides.slick({
              slidesToShow: 1,
              arrows: true,
              dots: true,
              adaptiveHeight: true,
              infinite: false,
            });
          }
        }
      });
    });
  }
};
