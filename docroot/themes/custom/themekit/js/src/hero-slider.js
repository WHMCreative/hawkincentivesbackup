/**
 * @file
 * Slick
 */
import $ from 'jquery';
import { Slick } from "slick-carousel/slick/slick";

Drupal.behaviors.heroSlider = {
  attach: function (context, settings) {
    let $content = $('.paragraph--type--compound-slider', context);
    if(!$content.length) return;

    const time = 5;

    $content.each(function () {

      let isPause,
        tick,
        percentTime,
        $nav = $content.find('.slider-nav'),
        $slides = $content.find('.field--name-field-p-slider-content');

      // Init Nav
      $nav.slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.field--name-field-p-slider-content',
        dots: false,
        focusOnSelect: true
      });

      // Init Slides
      $slides.slick({
        slidesToShow: 1,
        arrows: false,
        dots: false,
        fade: true,
        adaptiveHeight: false,
        infinite: true,
      });

      // Update nav for current slide
      $slides.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        $nav.find('.slick-slide').removeClass('slick-current');
        $nav.find('.slick-slide').eq(nextSlide).addClass('slick-current');
        startProgressbar();
      });


      // Start the progress bar
      function startProgressbar() {
        resetProgressbar();
        percentTime = 0;
        isPause = false;
        tick = setInterval(interval, 10);
      }

      // Animate progress bar based on time
      function interval() {
        if(isPause === false) {
          percentTime += 1 / (time+0.1);
          $nav.find('.slick-current .progress-bar .bar').css({
            width: percentTime+"%"
          });
          if(percentTime >= 100)
          {
            $slides.slick('slickNext');
            startProgressbar();
          }
        }
      }

      // Reset progress bar
      function resetProgressbar() {
        $nav.find('.progress-bar .bar').css({
          width: 0+'%'
        });
        clearTimeout(tick);
      }

      // Starts progress bar and timing for slides
      startProgressbar();
    });

  }
};
