/**
 * @file
 * Slick
 */
import $ from 'jquery';
import { Slick } from "slick-carousel/slick/slick";

Drupal.behaviors.slickCustom = {
  attach: function (context, settings) {
    const tabContentSelector = '.paragraph--type--compound-tabbed-content .field--name-field-p-tab-content';
    const tabNavSelector = '.slick-tab-nav';
    const tabContent = $(tabContentSelector, context);
    if(!tabContent.length) return;
    const tabLabel = tabContent.find('.field--name-field-label');
    let tabNavElement;

    let buildNav = (labels, place) => {
      const result = $.Deferred();
      tabNavElement = '';

      labels.each((i, el) => {
        tabNavElement += `<a href="#" class="slick-tab-nav--element">${$(el).text()}</a>`;
      });
      place.after(`<div class="slick-tab-nav">${tabNavElement}</div>`);

      return result;
    };

    let buildNavCount = (labels, place) => {
      let tabNavElementNumbers = '',
          tabNavElementLabels = '';

      labels.each((i, el) => {
        tabNavElementNumbers += `<span>${i + 1}/${labels.length}</span>`;
        tabNavElementLabels += `<span>${$(el).text()}</span>`;
      });

      if (!place.find('.slick-counter-numbers').length) {
        place.prepend(`<div class="slick-counter-numbers">${tabNavElementNumbers}</div>`);
      }
      if (!place.find('.slick-counter-labels').length) {
        place.prepend(`<div class="slick-counter-labels">${tabNavElementLabels}</div>`);
      }

      // Init
      $('.slick-counter-numbers span:nth-of-type(1)').addClass('active');
      $('.slick-counter-labels span:nth-of-type(1)').addClass('active');
    };

    let buildSlick = (mainSelector, navSelector, labelQuantity) => {
      $(mainSelector).slick({
        slidesToShow: 1,
        arrows: false,
        fade: true,
        adaptiveHeight: true,
        asNavFor: navSelector,
        responsive: [
          {
            breakpoint: 800,
            settings: {
              dots: true,
            }
          },
        ]
      });

      $(navSelector).slick({
        slidesToShow: labelQuantity.length,
        asNavFor: mainSelector,
        dots: false,
        centerMode: true,
        focusOnSelect: true,
      });

      // Set tabindex to 0 after slick init
      $(`${navSelector}--element`).each((i, el) => {
        $(el).attr('tabindex', '0');
      });
    };

    $(document).on('click', '.slick-tab-nav--element', (event) => {
      event.preventDefault();

      // Set tabindex to 0 after click
      $('.slick-tab-nav--element').each((i, el) => {
        $(el).attr('tabindex', '0');
      });
    });

    $(document).on('click', '.slick-dots button', (event) => {
      const index = $(event.currentTarget).parent().index();

      $(event.currentTarget).closest('.paragraph').find(`.slick-counter-numbers span`).removeClass('active');
      $(event.currentTarget).closest('.paragraph').find(`.slick-counter-numbers span:nth-of-type(${index + 1})`).addClass('active');

      $(event.currentTarget).closest('.paragraph').find(`.slick-counter-labels span`).removeClass('active');
      $(event.currentTarget).closest('.paragraph').find(`.slick-counter-labels span:nth-of-type(${index + 1})`).addClass('active');
    });

    // Now call the functions one after the other
    buildNav(tabLabel, tabContent).done( buildSlick(tabContentSelector, tabNavSelector, tabLabel) );

    $(window).on('load resize orientationchange', function() {
      if ($(window).width() <= 800 && !$('.slick-counter-numbers').length) {
        buildNavCount(tabLabel, $(tabNavSelector));
      }
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
          i = (currentSlide ? currentSlide : 0) + 1;
        if ($count.length == 0) {
          $this.find('> .paragraph-content').append('<div class="slick-counter">' + i + '/' + slick.slideCount + '</div>');
        }
        $count.text(i + '/' + slick.slideCount);
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
