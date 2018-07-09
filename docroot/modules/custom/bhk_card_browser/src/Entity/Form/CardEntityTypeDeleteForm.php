<?php

namespace Drupal\bhk_card_browser\Entity\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete Card type entities.
 */
class CardEntityTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.card_type.collection');
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
    $num_forms = $this->entityTypeManager->getStorage('card')->getQuery()
      ->condition('type', $this->entity->id())
      ->count()
      ->execute();

    if (!empty($num_forms)) {
      $common = ' You can not remove this card bundle until you have removed all of the %type cards.';
      $single = '%type bundle is used by 1 card on your site.' . $common;
      $multiple = '%type bundle is used by @count cards on your site.' . $common;
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
