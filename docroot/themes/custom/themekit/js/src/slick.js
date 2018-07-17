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
    const tabLabel = tabContent.find('.field--name-field-label');
    let tabNavElement = '';

    let buildNav = (labels, placeAfter) => {
      const result = $.Deferred();

      labels.each((i, el) => {
        tabNavElement += `<a href="#" class="slick-tab-nav--element">${$(el).text()}</a>`;
      });
      placeAfter.after(`<div class="slick-tab-nav">${tabNavElement}</div>`);

      return result;
    };

    let buildSlick = (mainSelector, navSelector) => {
      $(mainSelector).slick({
        slidesToShow: 1,
        arrows: false,
        fade: true,
        adaptiveHeight: true,
        asNavFor: navSelector,
      });

      $(navSelector).slick({
        slidesToShow: tabLabel.length,
        asNavFor: mainSelector,
        dots: false,
        centerMode: true,
        focusOnSelect: true,
        // responsive: [{
        //   breakpoint: 1024,
        //   settings: {
        //     slidesToShow: 1,
        //   }
        // }]
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

    // Now call the functions one after the other
    buildNav(tabLabel, tabContent).done( buildSlick(tabContentSelector, tabNavSelector) );

  }
};

/*Drupal.behaviors.mediaTile = {
  attach: function (context, settings) {
    let $content = $('.paragraph--type--compound-media-title-bar .field--name-field-p-media-tile-content', context);
    if(!$content.length) return;

    $content.each(function () {

      let $this = $(this);

      $this.on('init reInit afterChange', function(event, slick, currentSlide, nextSlide) {
        let $count = $this.find('.slick-counter'),
          i = (currentSlide ? currentSlide : 0) + 1;
        if ($count.length == 0) {
          $this.append('<div class="slick-counter">' + i + '/' + slick.slideCount + '</div>');
        }
        $count.text(i + '/' + slick.slideCount);
      });

      $this.slick({
        slidesToShow: 1,
        arrows: true,
        dots: true,
        adaptiveHeight: true,
        infinite: false,
        mobileFirst: true,
        responsive: [
          {
            breakpoint: 768,
            settings: 'unslick'
          }
        ]
      });
    });
  }
};*/
