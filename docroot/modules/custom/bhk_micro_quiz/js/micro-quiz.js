/**
 * @file
 * Handles MicroQuiz dynamic question and result transitions.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.microQuiz = {

    attach: function (context, settings) {

      let answerIds = [];

      /**
       * Save the answer to the current question.
       *
       * @param {String} key
       *   Cookie name.
       * @param {Object} activeQuestion
       *   Active Question jquery object.
       */
      const saveAnswer = function (key, activeQuestion) {
        let domain = "." + document.domain,
          $selectedRadio = activeQuestion.find('input[name=microquiz_question]:checked'),
          $selectedAnswer = $selectedRadio.next('label').find('.micro-quiz-answer'),
          selectedAnswerMarketoValue = $selectedAnswer.attr('data-marketo-value'),
          selectedAnswerId = $selectedAnswer.attr('data-answer-id');

        if (typeof(selectedAnswerMarketoValue) !== 'undefined') {
          answerIds.push(selectedAnswerId);

          $.cookie(key, selectedAnswerMarketoValue, {
            expires: 365,
            path: '/',
            domain: domain
          });
        }
      };

      /**
       * Retrieve the quiz results.
       *
       * @param {Object} activeQuestion
       *   Active Question jquery object.
       * @param {Boolean} saveAnswers
       *   Setting this to true will save answers in a cookie.
       */
      const setQuizResults = function (activeQuestion, saveAnswers) {

        // Send a request to retrieve the quiz results.
        $.ajax({
          type: "POST",
          url: "/micro_quiz/results",
          data: {
            mq_intention: $.cookie('mq_intention'),
            mq_quantity: $.cookie('mq_quantity'),
            answers: answerIds,
          },
          success: function (data, text_status) {

            let result = data['result'],
              mainWrapper = activeQuestion.parents('.paragraph--type--microquiz-quiz .quiz-wrapper');

            // Save answers.
            if (saveAnswers) {
              $.cookie('mq_answers', answerIds, {
                expires: 365,
                path: '/',
                domain: "." + document.domain
              });
            }

            if (mainWrapper.length) {
              mainWrapper.replaceWith(result);
            }
          }
        });
      };

      /**
       * Initialize the functionality.
       */
      const init = function () {

        // Enable submission buttons upon answer selection.
        $('input[name=microquiz_question]', context).once('answer-select').change(function () {
          let $active_question = $(this).parents('.active-question');

          if ($active_question.length) {
            $active_question.find('.form-submit')
              .removeClass('is-disabled')
              .removeAttr('disabled');
          }
        });

        // Next question submission handler.
        $('.quiz-action-next', context).once('quiz-next-trigger').click(function (e) {
          e.preventDefault();

          let $activeQuestion = $(this).parents('.active-question');

          if ($activeQuestion.length) {

            // Grab and save marketo data from selected answer.
            saveAnswer('mq_intention', $activeQuestion);

            // Switch to the next question.
            $activeQuestion
              .removeClass('active-question')
              .addClass('hidden')
              .next('.paragraph--type--microquiz-question')
              .removeClass('hidden')
              .addClass('active-question');
          }
        });

        // Final question submission handler.
        $('.quiz-action-finish', context).once('quiz-finish-trigger').click(function (e) {
          e.preventDefault();

          let $activeQuestion = $(this).parents('.active-question');

          if ($activeQuestion.length) {

            // Grab and save marketo data from selected answer.
            saveAnswer('mq_quantity', $activeQuestion);
            setQuizResults($activeQuestion, true);
          }
        });
      };

      // Initialize the functionality.
      init();
    }
  };

})(jQuery, Drupal);