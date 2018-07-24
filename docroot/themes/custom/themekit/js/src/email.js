/**
 * @file
 * Email manipulations
 */
import $ from 'jquery';

Drupal.behaviors.emailManipulations = {
  attach: function (context, settings) {
    const component = $('.paragraph--type--reference-featured-insight .node--type-insight', context);
    const labelText = Drupal.t('Step 1/2  •  Email Address');
    const btnText = Drupal.t('Download Now');
    const validationText = Drupal.t('Must be valid email. example@yourdomain.com');

    /**
     * Validate form.
     *
     * @param {object} element
     *   Form.
     */
    const validateDownloadForm = (element) => {
      const email = element.find('[name="email"]');
      const emailVal = email.val();
      const errorElement = email.next('.error-msg');

      if (emailVal === '') {
        errorElement.addClass('showed');
        return false;
      } else {
        errorElement.removeClass('showed');
        return true;
      }
    };

    /**
     * Initialise focus and value trackers for theming.
     *
     * @param {object} input
     *   Input object to process.
     */
    const setInputStateTracker = (input) => {
      let item = $('.form-item'),
        states = 'propertychange change paste input';

      input.on('focus', (e) => {
        $(e.currentTarget).closest(item).addClass('focus-form-item');
      }).on('blur', (e) => {
        $(e.currentTarget).closest(item).removeClass('focus-form-item');
      });

      input.on(states, (e) => {
        let $focusedItem = $(e.currentTarget),
          textVal = $focusedItem.val();

        if (textVal === '' || textVal.length < 1) {
          $focusedItem.closest(item).removeClass('has-value');
        } else {
          $focusedItem.closest(item).addClass('has-value');
        }
      });
    };

    // Build form
    if (component.length) {
      component.each((i, el) => {
        let $this = $(el);
        let componentId = $this.attr('data-id');
        let form = `<form class="download-form" action="/node/${componentId}" method="get">
                      <div class="form-item">
                        <label for="email--download-form" class="form-required">${labelText}</label>
                        <input type="email" id="email--download-form" name="email" value="">
                        <div class="error-msg">${validationText}</div>
                      </div>
                      <div class="form-actions">
                        <input type="submit" value="${btnText}">
                      </div>
                    </form>`;

        $this.find('.node--content').append(form);

        $this.find('.node--content .download-form').on('submit', (e) => {
          return validateDownloadForm($(e.currentTarget));
        });
      });

      setInputStateTracker(component.find('.form-item input'));
    }

    // Auto set up email from url
    const setToForm = $('.node--type-insight .paragraph--type--reference-marketo-form form', context);
    const setToFormId = setToForm.attr('data-form-id');
    const url = window.location.href;
    const urlTargetString = '?email=';
    let emailString = '';

    if (url.indexOf(urlTargetString) + 1) {
      // When several parameters are in the query
      if (url.indexOf('&') + 1) {
        emailString = url.split(urlTargetString)[1].split('&')[0];
      } else {
        emailString = url.split(urlTargetString)[1];
      }
    }

    // Fill in the email field after the form is rendered
    $(setToForm).on('whenFormElRendered' + setToFormId, () => {
      const setToFormEmail = setToForm.find('[type="email"]');

      if (emailString.length) {
        setToFormEmail.closest('.marketo-form-item').addClass('has-value');
        setToFormEmail.val(emailString);
      }
    });

  }
};
