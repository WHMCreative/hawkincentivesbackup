/**
 * @file
 * Handles Marketo form injections and general functionality.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.marketoForms = {

    attach: function (context, settings) {

      let marketoConfig;

      /**
       * Embed Marketo form.
       */
      const init = function () {

        // Integrate with the GDPR module if it is installed.
        let cookiesAccepted = true;
        if (typeof GDPR !== 'undefined') {
          cookiesAccepted = GDPR.cookiesAccepted();
        }

        if (cookiesAccepted  && typeof Munchkin !== 'undefined') {

          // Make sure Marketo cookie is added if Munchkin is set.
          let marketoCookie = $.cookie('_mkto_trk');

          if (marketoCookie === undefined) {
            Munchkin.init(marketoConfig.munchkinId);
          }
        }
        else if (!cookiesAccepted) {
          let domain = "." + document.domain;
          $.removeCookie('_mkto_trk', { path: '/', domain: domain });
        }

        // Initialize Marketo Form.
        initMarketoForm();

        // Inject the form.
        MktoForms2.whenReady(function (form) {
          let $form = $('#' + marketoConfig.htmlId, context);
          let $formWrapper = $form.parent(marketoConfig.formWrapper);

          if (!$form.hasClass('processed-marketo')) {
            // Replace placeholder form with new form, set ID back to original
            // ID and add processed class.
            $form.replaceWith(form.getFormElem().attr('id', marketoConfig.htmlId).addClass('processed-marketo'));
            $formWrapper.once("marketo-injected").trigger('marketoProcessed', [form]);

            // Add field name class for form row.
            form.getFormElem().find('.mktoField').each(function (e) {
              let elementId = $(this).attr('id');
              if (typeof elementId !== 'undefined') {
                $(this).parents('.mktoFormRow').addClass('mktoField' + elementId);
              }

              if($(this).is('input:not([type=checkbox])') && $(this).is('input:not([type=radio])')) {
                setInputStateTracker($(this), form.getFormElem());
              }

              if($(this).is('select') || $(this).is('textarea')) {
                $(this).parents('.mktoFieldWrap').addClass('marketo-form-item marketo-focus-form-item');
              }

              if($(this).is('input[type=checkbox]')) {
                $(this).parents('.mktoFieldWrap').addClass('marketo-checkbox');
              }

              if($(this).is('input[type=radio]')) {
                $(this).parents('.mktoFieldWrap').addClass('marketo-radio');
              }
            });
          }

          if (typeof(marketoConfig.removeSourceStyles) !== 'undefined' && marketoConfig.removeSourceStyles) {
            removeMarketoSourceStylesheets(form);
          }
        });
      };

      /**
       * Retrieve Marketo Settings.
       *
       * @return {Array}
       *   Array of defined Marketo Settings.
       */
      const getMarketoFormsSettings = function () {
        if (typeof(settings.marketoForms) !== 'undefined' && settings.marketoForms) {

          return settings.marketoForms;
        }

        return [];
      };

      /**
       * Initialise focus and value trackers for theming.
       *
       * @param {object} input
       *   Input object to process.
       */
      const setInputStateTracker = function (input, form) {
        input.parents('.mktoFieldWrap').addClass('marketo-form-item');

        let item = $('.marketo-form-item', context),
          states = 'propertychange change paste input';

        input.focus(function () {
          $(this).closest(item).addClass('marketo-focus-form-item');
        }).blur(function () {
          $(this).closest(item).removeClass('marketo-focus-form-item');
        });

        input.bind(states, function () {
          let text_val = $(this).val();
          if (text_val === "" || text_val.length < 1) {
            $(this).closest(item).removeClass('has-value');
          } else {
            $(this).closest(item).addClass('has-value');
          }
        });

        input.filter(function () {
          return this.value;
        }).closest(item).addClass('has-value');
      };

      /**
       * Initialize Marketo form.
       */
      const initMarketoForm = function () {
        // Get the form with the html id from settings.
        let $form = $("form#" + marketoConfig.htmlId, context);

        // Process the form if it's not initialized yet.
        if ($form.length && !$form.hasClass('init')) {

          $form.addClass('init');

          let $formWrapper = $form.parent(marketoConfig.formWrapper);

          // Check if the form has already been loaded from Marketo first.
          let form = MktoForms2.getForm(marketoConfig.formId);

          if (form) {
            $form.replaceWith(form.getFormElem());
            $formWrapper.once('marketo-injected').trigger('marketoProcessed', [form]);
          }
          // If form has not been loaded yet, load it from Marketo.
          else {
            MktoForms2.loadForm(marketoConfig.instanceHost, marketoConfig.munchkinId, marketoConfig.formId, function (form) {

              // Fetch any prefill data from Marketo.
              let marketoCookie = $.cookie('_mkto_trk');

              if (marketoCookie !== undefined) {

                $.ajax({
                  type: "POST",
                  url: "/marketo/prefill",
                  data: {
                    trkValue: marketoCookie,
                    formFields: form.getValues()
                  },
                  success: function(data, text_status) {

                    let prefillValues = {};
                    let currentValues = form.getValues();

                    for (let key in data) {

                      // Skip loop if the property is from prototype
                      if (!data.hasOwnProperty(key)) continue;

                      // Skip if the filled has already been pre-filled.
                      if (currentValues[key]) continue;

                      prefillValues[key] = data[key];
                    }

                    // Prefill data.
                    form.setValues(prefillValues);

                    // Mark pre-filled elements as having data. For theming.
                    let filledValues = form.vals(),
                      formElem = form.getFormElem();

                    formElem.find('input').each( function() {
                      let elemName = $(this).attr('name');

                      if (
                        typeof(elemName) !== 'undefined'
                        && $(this).attr('type') !== 'hidden'
                        && typeof(filledValues[elemName]) !== 'undefined'
                        && filledValues[elemName] !== ''
                      ) {

                        $(this).closest('.marketo-form-item').addClass('has-value');
                      }
                    });
                  }
                });
              }

              // Add/set hidden fields if settings for them were added.
              if (typeof(marketoConfig.hiddenFields) !== 'undefined' && marketoConfig.hiddenFields) {
                form.addHiddenFields(marketoConfig.hiddenFields);
              }

              $formWrapper.once('marketo-loaded').trigger('marketoLoad', [form]);

              // On successful form submission, check for settings to determine
              // next steps.
              form.onSuccess(function (values, followUpUrl) {

                $formWrapper.once('marketo-submitted').trigger('marketoSubmit', [form, values, followUpUrl]);

                // Run all specified submission callbacks.
                if (typeof(marketoConfig.submissionCallbacks) !== 'undefined' && marketoConfig.submissionCallbacks) {
                  for (let callbackValue in marketoConfig.submissionCallbacks) {

                    if (!marketoConfig.submissionCallbacks.hasOwnProperty(callbackValue)) continue;

                    let marketoSubmissionCallback = marketoConfig.submissionCallbacks[callbackValue];

                    if ($.isFunction(Drupal.behaviors.marketoForms[marketoSubmissionCallback])) {
                      Drupal.behaviors.marketoForms[marketoSubmissionCallback](form, marketoConfig, values);
                    }
                  }
                }

                // Prevent the default redirects that could be set from within
                // Marketo if a form has been set to use a different submission
                // behavior.
                if (typeof(marketoConfig.skipMarketoRedirects) !== 'undefined' && marketoConfig.skipMarketoRedirects) {
                  return false;
                }
              });
            });
          }
        }
      };

      /**
       * Remove Marketo-sourced stylesheets.
       *
       * @param {object} form
       *   Marketo form object.
       *
       * @see http://developers.marketo.com/javascript-api/forms/api-reference/
       */
      const removeMarketoSourceStylesheets = function (form) {
        let formEl = form.getFormElem()[0],
          $form = $("form#" + marketoConfig.htmlId, context);

        // Remove inline styles that Marketo adds to most elements.
        $('*[class^="mkto"][style]').removeAttr('style');
        $form.removeAttr('style');

        // Remove Marketo "required" divs and add Drupal's "form-required" class
        // to form labels instead. Remove mktoClear, mktoOffset, mktoGutter
        // empty div containers.
        $('.mktoAsterix, .mktoClear, .mktoOffset, .mktoGutter', context).remove();
        $('.mktoRequiredField .mktoLabel', context).addClass('form-required');

        let arrayFrom = Function.prototype.call.bind(Array.prototype.slice);

        // Disable remote stylesheets and local form styles.
        let styleSheets = arrayFrom(document.styleSheets);
        styleSheets.forEach(function (ss) {
          if ([mktoForms2BaseStyle, mktoForms2ThemeStyle].indexOf(ss.ownerNode) != -1 || formEl.contains(ss.ownerNode)) {
            ss.disabled = true;
          }
        });

        let mktoInlineStyles = $('#mktoForms2ThemeStyle').next('style');
        if (mktoInlineStyles.length > 0) {
          mktoInlineStyles.remove();
        }
      };

      /**
       * Initiate Marketo through an alternative instance host.
       *
       * This is mostly used to bypass Firefox tracking protection, but will
       * also fire in case original forms js was not successfully loaded.
       */
      const initProxyMarketo = function () {
        let s = document.createElement('script');
        s.onload = marketoPreInit;
        s.setAttribute('type', 'text/javascript');
        s.setAttribute('src', marketoConfig.instanceHost + '/js/forms2/js/forms2.min.js');
        document.getElementsByTagName('head')[0].appendChild(s);
      };

      /**
       * Run final checks before initializing the Marketo Forms injection.
       *
       * This function is here to provide possible integration with other
       * modules if there's a need to delay Marketo initialization.
       */
      const marketoPreInit = function () {

        // If we still don't have Marketo Assets loaded at this time, load
        // custom message and prevent further actions.
        if (typeof MktoForms2 === 'undefined') {
          let $form = $("form#" + marketoConfig.htmlId, context);

          if ($form.length > 0 && typeof marketoConfig.loadErrorMessage !== 'undefined') {
            let $formWrapper = $form.parent(marketoConfig.formWrapper);
            $formWrapper.addClass('no-marketo');
            $form.replaceWith(marketoConfig.loadErrorMessage);
            return false;
          }
        }

        // Delay init if GDPR module is installed.
        if (typeof GDPR !== 'undefined' && !GDPR.initialisationComplete) {
          $(window).on("gdpr:load", function () {
            init();
          });
        }
        else {
          init();
        }
      };

      // Init Marketo embed procedure.
      marketoConfig = getMarketoFormsSettings();
      if (typeof MktoForms2 === 'undefined') {
        marketoConfig.instanceHost = marketoConfig.alternativeInstanceHost;
        initProxyMarketo();
      }
      else {
        marketoPreInit();
      }

    },
    /**
     * Submission callback for default handler.
     *
     * Replaces the initial marketo entity with a "thank you" component, if it
     * has been added.
     *
     * @param {object} form
     *   Marketo form object.
     * @param {object} marketoConfig
     *   Marketo configuration.
     * @param values
     *   Submitted values.
     */
    submitMarketoForm: function submitMarketoForm(form, marketoConfig, values) {
      // Replace the form with a Thank You component if it has been set.
      if (typeof(marketoConfig.thankYouComponent) !== 'undefined' && marketoConfig.thankYouComponent) {

        let $form = $('#' + marketoConfig.htmlId);
        let $marketoWrapper = $form.closest(marketoConfig.entityWrapper);

        $marketoWrapper.replaceWith(marketoConfig.thankYouComponent).addClass('marketo-form-submitted');
      }
    }
  };

})(jQuery, Drupal);
