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
     * Open a marketo form in modal and fill a email field.
     *
     * @param {object} elem
     *   Sibling element.
     * @param {object} emailValue
     *   email value.
     */
    const showMarketoForm = (elem, emailValue) => {
      // Open the Marketo Form in modal
      let commonParent = elem.parents('.field--name-field-p-form'),
        modalSrc = commonParent.find('.paragraph--type--reference-marketo-form');
      if (modalSrc.length) {
        $.magnificPopup.open({
          items: {
            src: modalSrc,
            type: 'inline'
          },
          closeBtnInside: true,
        });
      }

      // Add the email value to the email field
      modalSrc.find('form [type="email"]').val(emailValue);

      const form = $(modalSrc).find('form');
      const formId = form.attr('data-form-id');

      // Replace the email value after prefill all marketo fields
      form.on('whenFormElRendered' + formId, () => {
        modalSrc.find('form [type="email"]').val(emailValue);
      });
    };

    /**
     * Validate the form.
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
        showMarketoForm(element, emailVal);
        return false;
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

    // Build the form
    if (component.length) {
      component.each((i, el) => {
        let $this = $(el);
        let form = `<form class="download-form" action="javascript:void()" method="">
                      <div class="form-item">
                        <label for="email--download-form" class="form-required">${labelText}</label>
                        <input type="email" id="email--download-form" name="email" value="">
                        <div class="error-msg">${validationText}</div>
                      </div>
                      <div class="form-actions">
                        <input type="submit" value="${btnText}">
                      </div>
                    </form>`;

        $this.find('.node--content .field--name-field-p-form').append(form);

        $this.find('.node--content .download-form').on('submit', (e) => {
          return validateDownloadForm($(e.currentTarget));
        });
      });

      setInputStateTracker(component.find('.form-item input'));
    }

  }
};
