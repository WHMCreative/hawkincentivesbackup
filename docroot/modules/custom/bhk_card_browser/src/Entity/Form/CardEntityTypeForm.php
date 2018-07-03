<?php

namespace Drupal\bhk_card_browser\Entity\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CardEntityTypeForm.
 */
class CardEntityTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $card_type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $card_type->label(),
      '#description' => $this->t('The human-readable name of this Card Type. This name must be unique.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $card_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\bhk_card_browser\Entity\CardEntityType::load',
      ],
      '#disabled' => !$card_type->isNew(),
      '#description' => $this->t('A unique machine-readable name for this card type. It must only contain lowercase letters, numbers, and underscores.'),
    ];

    $form['description'] = [
      '#title' => $this->t('Description'),
      '#type' => 'textarea',
      '#default_value' => $card_type->getDescription(),
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $card_type = $this->entity;
    $status = $card_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label Card type.', [
          '%label' => $card_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label Card type.', [
          '%label' => $card_type->label(),
        ]));
    }

    $form_state->setRedirect('entity.card_type.collection');
  }

}
