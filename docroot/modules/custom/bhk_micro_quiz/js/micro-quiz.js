/**
 * @file
 * Handles MicroQuiz dynamic question and result transitions.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.microQuiz = {

    attach: function (context, settings) {

      $('.active-question .quiz-action-next', context).once('quiz-next-trigger').click(function (e) {
        e.preventDefault();

        $('.active-question')
          .removeClass('active-question')
          .addClass('hidden')
          .next('.quiz-question-wrapper')
          .removeClass('hidden')
          .addClass('active-question');
      });
    }
  };

})(jQuery, Drupal);