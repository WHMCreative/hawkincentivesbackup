<?php

namespace Drupal\e3_marketo\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Marketo form entity entities.
 */
class MarketoFormEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
