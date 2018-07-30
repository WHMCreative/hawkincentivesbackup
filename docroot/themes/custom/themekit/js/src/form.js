/**
 * @file
 * Form
 */
import $ from 'jquery';

Drupal.behaviors.formManipulations = {
  attach: function (context, settings) {
    // Marketo form
    const mktoFieldSelector = '.mktoFieldDescriptor';
    const mktoFieldSelectSelector = '.mktoFieldDescriptor select';

    // Put a message element into a label
    $(document).on('click keyup', mktoFieldSelector, (event) => {
      const mktoField = $(event.currentTarget);
      const mktoFieldLabel = mktoField.find('label');
      const mktoFieldMsg = mktoField.find('.mktoError');

      if (mktoFieldMsg.length) {
        mktoFieldMsg.appendTo(mktoFieldLabel);
      }
    });

    // Select onFocus
    $(document).on('focus', mktoFieldSelectSelector, (event) => {
      const mktoFieldSelect = $(event.target);
      const mktoFieldLabel = mktoFieldSelect.closest(mktoFieldSelector).find('label');
      const mktoFieldMsg = mktoFieldSelect.closest(mktoFieldSelector).find('.mktoError');

      if (mktoFieldMsg.length) {
        const msgClone = mktoFieldMsg.clone();

        msgClone.appendTo(mktoFieldLabel);
      }
    });

    // Select onBlur
    $(document).on('blur', mktoFieldSelectSelector, (event) => {
      const mktoFieldSelect = $(event.target);
      const mktoFieldLabel = mktoFieldSelect.closest(mktoFieldSelector).find('label');

      if (mktoFieldLabel.find('.mktoError').length) {
        mktoFieldLabel.find('.mktoError').remove();
      }
    });
  }
};
