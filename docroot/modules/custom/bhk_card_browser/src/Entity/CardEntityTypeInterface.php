<?php

namespace Drupal\bhk_card_browser\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Card type entities.
 */
interface CardEntityTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the description of the entity.
   *
   * @return string|null
   *   Description of the entity, or NULL if there is no description defined.
   */
  public function getDescription();

}
