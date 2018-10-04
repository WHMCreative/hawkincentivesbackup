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
            Buying_Stage: $.cookie('Buying_Stage'),
            mq_quantity: $.cookie('mq_quantity'),
            answers: answerIds,
          },
          success: function (data, text_status) {

            if (data['hasForm']) {
              settings.marketoForms[data['marketoFormKey']][data['formSettingsKey']] = data['formSettings'];
            }

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
              Drupal.attachBehaviors();
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

        let $next = $('.quiz-action-next', context);
        // Next question submission handler.
        $next.each(function() {
          let $question = $(this);
          $question.once('quiz-next-trigger').click(function (e) {
            e.preventDefault();

            let $activeQuestion = $(this).parents('.active-question');
            let $parts = $activeQuestion.find('.micro-quiz-question').attr('id').split('--');
            let $question_index = ($parts.length > 1) ? $parts[1] : '1';

            if ($activeQuestion.length) {

              // Grab and save marketo data from selected answer.
              if ($question_index == '1') {
                saveAnswer('mq_intention', $activeQuestion);
              } else if ($question_index == '2') {
                saveAnswer('Buying_Stage', $activeQuestion);
              }

              // Switch to the next question.
              $activeQuestion
                .removeClass('active-question')
                .addClass('hidden')
                .css('transform', 'translateX(' + $question_index * -100 + '%)')
                .next('.paragraph--type--microquiz-question')
                .css('transform', 'translateX(' + $question_index * -100 + '%)')
                .removeClass('hidden')
                .addClass('active-question');
            }
          });

        });

        let $back = $('.quiz-action-back', context);
        // Back question submission handler.
        $back.each(function() {
          let $question = $(this);
          $question.click(function (e) {
            e.preventDefault();

            let $activeQuestion = $(this).parents('.active-question');
            let $parts = $activeQuestion.find('.micro-quiz-question').attr('id').split('--');
            let $question_index = ($parts.length > 1) ? $parts[1] : '1';

            $activeQuestion
              .removeClass('active-question')
              .addClass('hidden')
              .css('transform', 'translateX(' + ($question_index - 2) * -100 + '%)')
              .prev('.paragraph--type--microquiz-question')
              .css('transform', 'translateX(' + ($question_index - 2) * -100 + '%)')
              .removeClass('hidden')
              .addClass('active-question');

          });
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