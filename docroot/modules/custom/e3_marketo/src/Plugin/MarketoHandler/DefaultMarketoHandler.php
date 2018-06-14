<?php

namespace Drupal\e3_marketo\Plugin\MarketoHandler;

use Drupal\Component\Utility\Html;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Template\Attribute;
use Drupal\e3_marketo\Annotation\MarketoHandler;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;
use Drupal\e3_marketo\Plugin\MarketoHandlerBase;

/**
 * Default handler to build output for Marketo Forms.
 *
 * @MarketoHandler(
 *   id = "default_marketo_handler",
 *   label = @Translation("Default Marketo Handler"),
 *   description = @Translation("Default Marketo handler to attach a base Marketo form with prefill support."),
 *   bundles = {},
 *   priority = 100
 * )
 */
class DefaultMarketoHandler extends MarketoHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function embedMarketoForm(MarketoFormEntityInterface $marketo_form, array &$build) {
    $marketo_form_id = $marketo_form->getFormId();
    $html_id = "marketo-form-{$marketo_form_id}";

    // Do not embed the form if key Marketo settings are missing.
    if (!$this->verifyMarketoSettings()) {
      return;
    }

    // Only run the default processing if dynamic parameters has not been set or
    // if they were mistakenly cleared by one of the plugins.
    if (empty($build['marketo_form_embed']['#form_attributes'])
      || empty($build['marketo_form_embed']['#attached']['drupalSettings']['marketoForms'])) {

      $build['marketo_form_embed'] = [
        '#theme' => 'marketo_form_embed',
        '#form_attributes' => $this->getMarketoFormAttributes($marketo_form, $html_id),
        '#attached' => [
          'library' => ['e3_marketo/marketo-forms'],
          'drupalSettings' => [
            'marketoForms' => $this->getMarketoScriptParameters($marketo_form, $html_id),
          ],
        ],
      ];
    }
    else {
      // Otherwise only update the dynamic parameters.
      $this->alterScriptParameters($build['marketo_form_embed']['#attached']['drupalSettings']['marketoForms'], $marketo_form);
    }
  }

  /**
   * Retrieve script parameters to pass into Marketo embed script.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo form entity.
   * @param $html_id
   *   Html Id genrated for this marketo form.
   *
   * @return array
   *   Array of parameters to add to drupalSettings.
   */
  public function getMarketoScriptParameters(MarketoFormEntityInterface $marketo_form, $html_id) {
    $submission_callbacks = $this->getSubmissionCallbacks();

    $result = [
      'formWrapper' => '.marketo-form',
      'entityWrapper' => '.marketo-form-entity-ajax-wrapper',
      'munchkinId' => $this->marketoConfig->get('munchkin_id'),
      'instanceHost' => $this->marketoConfig->get('instance_host'),
      'htmlId' => $html_id,
      'formId' => $marketo_form->getFormId(),
      'removeSourceStyles' => $marketo_form->bundle->entity->getRemoveSourceStyles(),
      'submissionCallbacks' => $submission_callbacks,
      'alternativeInstanceHost' => $this->marketoConfig->get('alt_instance_host'),
      'loadErrorMessage' => $this->marketoConfig->get('load_error_message.value'),
    ];

    // Figure out if any redirects set up within Marketo should be skipped.
    if (!empty($submission_callbacks)) {
      $result['skipMarketoRedirects'] = TRUE;
    }

    // Check if Thank You component has been added.
    if ($marketo_form->hasField('field_submission_confirmation')
      && $marketo_form->get('field_submission_confirmation')->first()) {

      $submission_confirmation = $this->entityTypeManager
        ->getViewBuilder('marketo_form')
        ->viewField($marketo_form->get('field_submission_confirmation'), ['label' => 'hidden']);

      if ($submission_confirmation) {
        try {
          $submission_confirmation = $this->renderer->render($submission_confirmation);
          $result['thankYouComponent'] = $submission_confirmation;
        } catch (\Exception $e) {
          // TODO - add log entry?
        }
      }
    }

    // If this form is referenced via component, check if any hidden fields have
    // been set.
    if (!empty($marketo_form->_referringItem)) {
      $parent_entity = $marketo_form->_referringItem->getEntity();

      if ($parent_entity && $parent_entity->hasField('field_marketo_hidden_fields')) {

        $hidden_fields = $this->getMarketoHiddenFields($parent_entity);

        if ($hidden_fields) {
          $result['hiddenFields'] = $hidden_fields;
        }
      }
    }

    $this->alterScriptParameters($result, $marketo_form);

    return $result;
  }

  /**
   * Alter the list of parameters that will be sent to Marketo script.
   *
   * @param array $params
   *   Parameters list to alter.
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo Form entity.
   */
  public function alterScriptParameters(&$params, MarketoFormEntityInterface $marketo_form) {
    // Nothing to do here in this handler, but the function stub needs to exist
    // to avoid errors.
  }

  /**
   * Retrieve a set of Html parameters to pass into Marketo template.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo form entity.
   * @param $html_id
   *   Html Id genrated for this marketo form.
   *
   * @return \Drupal\Core\Template\Attribute
   *   Html attributes to pass into template as 'form_attributes'.
   */
  public function getMarketoFormAttributes(MarketoFormEntityInterface $marketo_form, $html_id) {
    $attributes = new Attribute();
    $attributes->setAttribute('id', $html_id);
    $attributes->setAttribute('data-form-id', $marketo_form->getFormId());
    $attributes->addClass('marketo-form-load');

    return $attributes;
  }

  /**
   * Check that all required Marketo settings have been set.
   *
   * @return bool
   *   TRUE if all required settings are available, FALSE otherwise.
   */
  public function verifyMarketoSettings() {
    if (!$this->marketoConfig->get('instance_host') || !$this->marketoConfig->get('munchkin_id')) {
      drupal_set_message($this->t("Marketo Form could not be loaded, because some of the required Marketo Settings are missing. Please, visit the @link to fill them.", [
        '@link' => Link::createFromRoute($this->t("Settings Page"), 'marketo_form.settings')->toString(),
      ]), 'error');

      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Retrieve all hidden fields set for the Marketo Form.
   *
   * @param \Drupal\Core\Entity\EntityInterface $parent_entity
   *   Parent entity of the Marketo Form.
   *
   * @return object
   *   An object with field names as properties. This is the way Marketo expects
   *   the hidden fields data to be set.
   */
  public function getMarketoHiddenFields(EntityInterface $parent_entity) {
    $result = [];

    // Check if any hidden fields have been set.
    if (!$parent_entity->get('field_marketo_hidden_fields')->isEmpty()) {

      foreach ($parent_entity->get('field_marketo_hidden_fields') as $hidden_field) {

        $paragraph = $hidden_field->entity;

        if ($paragraph->hasField('field_hidden_field_name') && $paragraph->hasField('field_hidden_field_value')) {
          $field_name = $paragraph->get('field_hidden_field_name')->value;
          $field_value = $paragraph->get('field_hidden_field_value')->value;

          $result[$field_name] = $field_value;
        }
      }
    }

    return (object) $result;
  }

  /**
   * Get callbacks for Marketo submission.
   *
   * Retrieve an array of callbacks that Marketo Embed script will execute upon
   * a successful Marketo form submission. These callbacks have to be added to
   * Drupal.behaviors.marketoForms.
   *
   * @return array
   *   Array of callbacks, keyed by the Plugin ID.
   */
  public function getSubmissionCallbacks() {
    return [
      $this->getPluginId() => 'submitMarketoForm',
    ];
  }
}
