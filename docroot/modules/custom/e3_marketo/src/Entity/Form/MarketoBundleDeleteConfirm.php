<?php

namespace Drupal\e3_marketo\Entity\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MarketoBundleDeleteConfirm
 *
 * @package Drupal\e3_marketo\Entity\Form
 */
class MarketoBundleDeleteConfirm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $num_forms = $this->entityTypeManager->getStorage('marketo_form')->getQuery()
      ->condition('bundle', $this->entity->id())
      ->count()
      ->execute();

    if (!empty($num_forms)) {
      $common = ' You can not remove this marketo form bundle until you have removed all of the %type marketo forms.';
      $single = '%type bundle is used by 1 marketo form on your site.' . $common;
      $multiple = '%type bundle is used by @count marketo forms on your site.' . $common;
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

}
