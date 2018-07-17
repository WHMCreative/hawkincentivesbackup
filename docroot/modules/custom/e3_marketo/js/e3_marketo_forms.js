/**
 * @file
 * Handles Marketo form injections and general functionality.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.marketoForms = {

    attach: function (context, settings) {

      // Marketo tracking cookie.
      let marketoCookie = $.cookie('_mkto_trk');

      /**
       * Embed Marketo form.
       *
       * @param {Array} marketoConfig
       *   Marketo form configuration array.
       */
      const init = function (marketoConfig) {

        // Integrate with the GDPR module if it is installed.
        let cookiesAccepted = true;
        if (typeof GDPR !== 'undefined') {
          cookiesAccepted = GDPR.cookiesAccepted();
        }

        if (cookiesAccepted  && typeof Munchkin !== 'undefined') {

          // Make sure Marketo cookie is added if Munchkin is set.
          if (marketoCookie === undefined) {
            Munchkin.init(marketoConfig.munchkinId);
          }
        }
        else if (!cookiesAccepted) {
          let domain = "." + document.domain;
          $.removeCookie('_mkto_trk', { path: '/', domain: domain });
        }

        // Initialize and inject Marketo Form.
        let $formInstances = $("form." + marketoConfig.htmlClass, context);

        if ($formInstances.length) {
          injectMarketoForms(marketoConfig);
        }

        // Operations to perfrorm when form is ready after being injected.
        MktoForms2.whenReady(function (form) {

          let $form = $('.' + marketoConfig.htmlClass, context);
          if ($form.length && !$form.hasClass('processed-marketo')) {

            // Add field name class for form row.
            form.getFormElem().find('.mktoField').each(function (e) {
              let $formField = $(this),
                elementName = $formField.attr('name');

              if (typeof elementName !== 'undefined') {
                $formField.parents('.mktoFormRow').addClass('mktoField' + elementName);
              }

              if($formField.is('input:not([type=checkbox])') && $formField.is('input:not([type=radio])')) {
                setInputStateTracker($formField);
              }

              if($formField.is('select') || $formField.is('textarea')) {
                $formField.parents('.mktoFieldWrap').addClass('marketo-form-item marketo-focus-form-item');
              }

              if($formField.is('input[type=checkbox]')) {
                $formField.parents('.mktoFieldWrap').addClass('marketo-checkbox');
              }

              if($formField.is('input[type=radio]')) {
                $formField.parents('.mktoFieldWrap').addClass('marketo-radio');
              }
            });
          }

          if (typeof(marketoConfig.removeSourceStyles) !== 'undefined' && marketoConfig.removeSourceStyles) {
            removeMarketoSourceStylesheets(form, marketoConfig);
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
      const setInputStateTracker = function (input) {
        input.parents('.mktoFieldWrap').addClass('marketo-form-item');

        let item = $('.marketo-form-item', context),
          states = 'propertychange change paste input';

        input.focus(function () {
          $(this).closest(item).addClass('marketo-focus-form-item');
        }).blur(function () {
          $(this).closest(item).removeClass('marketo-focus-form-item');
        });

        input.bind(states, function () {
          let $focusedItem = $(this),
            textVal = $focusedItem.val();

          if (textVal === "" || textVal.length < 1) {
            $focusedItem.closest(item).removeClass('has-value');
          } else {
            $focusedItem.closest(item).addClass('has-value');
          }
        });

        input.filter(function () {
          return this.value;
        }).closest(item).addClass('has-value');
      };

      /**
       * Chain load and inject all Marketo Forms.
       *
       * @param {Array} marketoConfig
       *   Configuration settings the for Form instance.
       */
      const injectMarketoForms = function(marketoConfig) {

        let arrayFrom = Function.prototype.call.bind(Array.prototype.slice),
          marketoFormDataAttr = "data-form-id";

        // Make labels unique for accessibility.
        MktoForms2.whenRendered(function (form) {
          let formEl = form.getFormElem()[0],
            randomSuffix = "_" + new Date().getTime() + Math.random();

          arrayFrom(formEl.querySelectorAll("label[for]")).forEach(function (labelEl) {
            let forEl = formEl.querySelector('[id="' + labelEl.htmlFor + '"]');
            if (forEl) {
              labelEl.htmlFor = forEl.id = forEl.id + randomSuffix;
            }
          });
        });

        let loadForm = MktoForms2.loadForm.bind(MktoForms2, marketoConfig.instanceHost, marketoConfig.munchkinId, marketoConfig.formId),
          formEls = arrayFrom(document.querySelectorAll("[" + marketoFormDataAttr + '="' + marketoConfig.formId + '"]'));

        // Chain load forms. This will ensure the same form can be loaded on a
        // page multiple times.
        (function loadFormCb(formEls) {

          // Retrieve the form
          let formEl = formEls.shift();
          formEl.id = "mktoForm_" + marketoConfig.formId;

          // Load the form.
          loadForm(function (form) {
            let dataInstance = formEl.getAttribute('data-instance');
            formEl.id = 'marketo-form-' + marketoConfig.formId + "-" + dataInstance;

            // Pre-fill the form.
            prefillMarketoForm(form);

            // Execute all post-load stuff for the form.
            marketoFormPostLoad(form, marketoConfig, dataInstance);

            if (formEls.length) {
              loadFormCb(formEls);
            }
          });
        })(formEls);
      };

      /**
       * Pre-fill Marketo Form.
       *
       * @param {Object} form
       *   Loaded Marketo Form object.
       */
      const prefillMarketoForm = function (form) {
        if (marketoCookie !== undefined) {

          $.ajax({
            type: "POST",
            url: "/marketo/prefill",
            data: {
              trkValue: marketoCookie,
              formFields: form.getValues()
            },
            success: function (data, text_status) {

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

              formElem.find('input').each(function () {
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
      };

      /**
       * Execute all post-load Marketo operations.
       *
       * @param {Object} form
       *   Marketo Form.
       * @param {Array} marketoConfig
       *   Marketo Form configuration.
       * @param {String} dataInstance
       *   Configuration instance number.
       */
      const marketoFormPostLoad = function(form, marketoConfig, dataInstance) {
        let instanceConfig = marketoConfig[dataInstance];

        // Add/set hidden fields if settings for them were added.
        if (typeof(instanceConfig.hiddenFields) !== 'undefined' && instanceConfig.hiddenFields) {
          form.addHiddenFields(instanceConfig.hiddenFields);
        }

        // On successful form submission, check for settings to determine
        // next steps.
        form.onSuccess(function (values, followUpUrl) {

          $(window).once('marketo-submitted').trigger('marketoSubmit', [form, values, followUpUrl]);

          // Run all specified submission callbacks.
          if (typeof(instanceConfig.submissionCallbacks) !== 'undefined' && instanceConfig.submissionCallbacks) {
            for (let callbackValue in instanceConfig.submissionCallbacks) {

              if (!instanceConfig.submissionCallbacks.hasOwnProperty(callbackValue)) continue;

              let marketoSubmissionCallback = instanceConfig.submissionCallbacks[callbackValue];

              if ($.isFunction(Drupal.behaviors.marketoForms[marketoSubmissionCallback])) {
                Drupal.behaviors.marketoForms[marketoSubmissionCallback](form, marketoConfig, values, dataInstance);
              }
            }
          }

          // Prevent the default redirects that could be set from within
          // Marketo if a form has been set to use a different submission
          // behavior.
          if (typeof(instanceConfig.skipMarketoRedirects) !== 'undefined' && instanceConfig.skipMarketoRedirects) {
            return false;
          }
        });
      };

      /**
       * Remove Marketo-sourced stylesheets.
       *
       * @param {object} form
       *   Marketo form object.
       * @param {Array} marketoConfig
       *   Marketo form config.
       *
       * @see http://developers.marketo.com/javascript-api/forms/api-reference/
       */
      const removeMarketoSourceStylesheets = function (form, marketoConfig) {
        let formEl = form.getFormElem()[0],
          $form = $("form." + marketoConfig.htmlClass, context);

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

        // Remove inline marketo custom fonts.
        let mktoInlineFonts = $('#mktoFontUrl');
        if (mktoInlineFonts.length > 0) {
          mktoInlineFonts.remove();
        }

        let mktoInlineStyles = $('#mktoForms2ThemeStyle').next('style'),
          mktoinlineFormStyles = $form.find('style');

        if (mktoInlineStyles.length > 0) {
          mktoInlineStyles.remove();
        }

        if (mktoinlineFormStyles.length > 0) {
          mktoinlineFormStyles.remove();
        }
      };

      /**
       * Initiate Marketo through an alternative instance host.
       *
       * This is mostly used to bypass Firefox tracking protection, but will
       * also fire in case original forms js was not successfully loaded.
       */
      const initProxyMarketo = function (marketoConfig) {
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
      const marketoPreInit = function (marketoConfig) {

        // If we still don't have Marketo Assets loaded at this time, load
        // custom message and prevent further actions.
        if (typeof MktoForms2 === 'undefined') {
          let $form = $("form." + marketoConfig.htmlClass, context);

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
            init(marketoConfig);
          });
        }
        else {
          init(marketoConfig);
        }
      };

      // Init Marketo embed procedure.
      let marketoSettingsAll = getMarketoFormsSettings();

      for (let marketoProp in marketoSettingsAll) {
        if (!marketoSettingsAll.hasOwnProperty(marketoProp)) continue;

        if (marketoProp === 'munchkinId') continue;

        let marketoConfig = marketoSettingsAll[marketoProp];

        if (typeof MktoForms2 === 'undefined') {
          marketoConfig.instanceHost = marketoConfig.alternativeInstanceHost;
          initProxyMarketo(marketoConfig);
        }
        else {
          marketoPreInit(marketoConfig);
        }
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

        let $form = form.getFormElem();
        let $marketoWrapper = $form.closest(marketoConfig.entityWrapper);

        $marketoWrapper.replaceWith(marketoConfig.thankYouComponent).addClass('marketo-form-submitted');
      }
    }
  };

})(jQuery, Drupal);
