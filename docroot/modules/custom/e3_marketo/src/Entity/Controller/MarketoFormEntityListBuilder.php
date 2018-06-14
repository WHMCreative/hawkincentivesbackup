<?php

namespace Drupal\e3_marketo\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Marketo form entity entities.
 *
 * @ingroup e3_marketo
 */
class MarketoFormEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Marketo form entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\e3_marketo\Entity\MarketoFormEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.marketo_form.edit_form',
      ['marketo_form' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
