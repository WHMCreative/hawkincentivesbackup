<?php

namespace Drupal\bhk_micro_quiz\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for MicroQuiz add/edit forms.
 *
 * @ingroup bhk_micro_quiz
 */
class MicroQuizForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface $entity */
    $entity = $this->entity;
    $form = parent::buildForm($form, $form_state);

    $operation = $this->operation;

    $form['#title'] = $this->t('@operation @title MicroQuiz', [
      '@operation' => ucfirst($operation),
      '@title' => $operation === 'add' ? $this->getBundleEntity()->label() : $entity->getName(),
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface $entity */
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label MicroQuiz.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label MicroQuiz.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.micro_quiz.canonical', ['micro_quiz' => $entity->id()]);
  }

}
