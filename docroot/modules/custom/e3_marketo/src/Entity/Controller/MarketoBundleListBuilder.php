<?php

namespace Drupal\e3_marketo\Entity\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a listing of marketo form bundle entities.
 *
 * @ingroup e3_marketo
 *
 * @see \Drupal\e3_marketo\Entity\MarketoFormEntityBundle
 */
class MarketoBundleListBuilder extends ConfigEntityListBuilder {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['description'] = [
      'data' => $this->t('Description'),
      'class' => [RESPONSIVE_PRIORITY_MEDIUM],
    ];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $entity */
    $row['label'] = [
      'data' => $entity->label(),
      'class' => ['menu-label'],
    ];
    $row['description']['data'] = ['#markup' => $entity->getDescription()];
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['table']['#empty'] = $this->t('No marketo form bundles available. @link.', [
      '@link' => Link::fromTextAndUrl($this->t('Add bundle'), Url::fromRoute('marketo_form_bundle.add'))->toString()
    ]);

    return $build;
  }

}
