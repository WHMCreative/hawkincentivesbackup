<?php

namespace Drupal\bhk_card_browser\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Card entities.
 */
class CardViewsData extends EntityViewsData {

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