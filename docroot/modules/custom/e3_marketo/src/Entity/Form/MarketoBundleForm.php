<?php

namespace Drupal\e3_marketo\Entity\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MarketoBundleForm
 *
 * @package Drupal\e3_marketo\Entity\Form
 */
class MarketoBundleForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    /** @var \Drupal\e3_marketo\Entity\MarketoFormEntityBundle $type */
    $form = parent::form($form, $form_state);
    $type = $this->entity;

    $form['label'] = [
      '#title' => $this->t('Name'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => $this->t('The human-readable name of this Marketo Form Bundle. This name must be unique.'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => ['Drupal\e3_marketo\Entity\MarketoFormEntityBundle', 'load'],
        'source' => ['label'],
      ],
      '#description' => $this->t('A unique machine-readable name for this marketo form bundle. It must only contain lowercase letters, numbers, and underscores.'),
    ];

    $form['description'] = [
      '#title' => $this->t('Description'),
      '#type' => 'textarea',
      '#default_value' => $type->getDescription(),
    ];

    $form['additional_settings'] = [
      '#type' => 'vertical_tabs',
      '#attached' => [
        'library' => ['e3_marketo/marketo_bundle_form'],
      ],
    ];

    $form['marketo_source_settings'] = [
      '#type' => 'details',
      '#title' => t('Marketo Source Settings'),
      '#weight' => 40,
      '#group' => 'additional_settings',
    ];

    $form['marketo_source_settings']['remove_source_styles'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove Marketo-sourced stylesheets'),
      '#description' => $this->t('Check in order to automatically remove all Marketo-sourced css from the forms of this bundle to make theming them easier.'),
      '#default_value' => $type->getRemoveSourceStyles(),
      '#weight' => 10,
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    if ($status == SAVED_UPDATED) {
      $this->messenger()->addStatus($this->t('Marketo Form Bundle @bundle successfully updated.', ['@bundle' => $form_state->getValue('label')]));
    }
    elseif ($status == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Marketo Form Bundle @bundle created successfully.', ['@bundle' => $form_state->getValue('label')]));
    }

    $form_state->setRedirect('entity.marketo_form_bundle.collection');
    return $this->entity;
  }
}