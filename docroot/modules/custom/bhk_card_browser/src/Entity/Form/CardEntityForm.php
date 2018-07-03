<?php

namespace Drupal\bhk_card_browser\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Card edit forms.
 *
 * @ingroup bhk_card_browser
 */
class CardEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\bhk_card_browser\Entity\CardEntity */
    $form = parent::buildForm($form, $form_state);

    $operation = $this->operation;

    $form['#title'] = $this->t('@operation @title Card', [
      '@operation' => ucfirst($operation),
      '@title' => $operation === 'add' ? $this->getBundleEntity()->label() : $this->entity->getName(),
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Card.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Card.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.card.canonical', ['card' => $entity->id()]);
  }

}
