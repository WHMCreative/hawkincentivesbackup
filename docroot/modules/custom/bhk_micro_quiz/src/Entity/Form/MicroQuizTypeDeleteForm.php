<?php

namespace Drupal\bhk_micro_quiz\Entity\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete MicroQuiz type entities.
 */
class MicroQuizTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete MicroQuiz bundle %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.micro_quiz_type.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $num_forms = $this->entityTypeManager->getStorage('micro_quiz')->getQuery()
      ->condition('type', $this->entity->id())
      ->count()
      ->execute();

    if (!empty($num_forms)) {
      $common = ' You can not remove this MicroQuiz bundle until you have removed all of the %type MicroQuiz entities.';
      $single = '%type bundle is used by 1 MicroQuiz on your site.' . $common;
      $multiple = '%type bundle is used by @count MicroQuiz entities on your site.' . $common;
      $replace = ['%type' => $this->entity->label()];

      $form['#title'] = $this->getQuestion();
      $form['description'] = [
        '#type' => 'container',
        '#markup' => $this->formatPlural($num_forms, $single, $multiple, $replace),
      ];

      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();

    $this->messenger()->addStatus(
      $this->t('content @type: deleted @label.',
        [
          '@type' => $this->entity->bundle(),
          '@label' => $this->entity->label(),
        ]
        )
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
