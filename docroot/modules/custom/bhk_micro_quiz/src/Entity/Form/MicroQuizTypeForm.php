<?php

namespace Drupal\bhk_micro_quiz\Entity\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MicroQuizTypeForm.
 */
class MicroQuizTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $micro_quiz_bundle = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $micro_quiz_bundle->label(),
      '#description' => $this->t('The human-readable name of this MicroQuiz bundle. This name must be unique.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $micro_quiz_bundle->id(),
      '#machine_name' => [
        'exists' => '\Drupal\bhk_micro_quiz\Entity\MicroQuizType::load',
      ],
      '#disabled' => !$micro_quiz_bundle->isNew(),
      '#description' => $this->t('A unique machine-readable name for this MicroQuiz bundle. It must only contain lowercase letters, numbers, and underscores.'),
    ];

    $form['description'] = [
      '#title' => $this->t('Description'),
      '#type' => 'textarea',
      '#default_value' => $micro_quiz_bundle->getDescription(),
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $micro_quiz_bundle = $this->entity;
    $status = $micro_quiz_bundle->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label MicroQuiz bundle.', [
          '%label' => $micro_quiz_bundle->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label MicroQuiz bundle.', [
          '%label' => $micro_quiz_bundle->label(),
        ]));
    }

    $form_state->setRedirect('entity.micro_quiz_type.collection');
  }

}
