<?php

namespace Drupal\bhk_card_browser\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Card entities.
 *
 * @ingroup bhk_card_browser
 */
interface CardEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Card name.
   *
   * @return string
   *   Name of the Card.
   */
  public function getName();

  /**
   * Sets the Card name.
   *
   * @param string $name
   *   The Card name.
   *
   * @return \Drupal\bhk_card_browser\Entity\CardEntityInterface
   *   The called Card entity.
   */
  public function setName($name);

  /**
   * Gets the Card creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Card.
   */
  public function getCreatedTime();

  /**
   * Sets the Card creation timestamp.
   *
   * @param int $timestamp
   *   The Card creation timestamp.
   *
   * @return \Drupal\bhk_card_browser\Entity\CardEntityInterface
   *   The called Card entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Card published status indicator.
   *
   * Unpublished Card are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Card is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Card.
   *
   * @param bool $published
   *   TRUE to set this Card to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\bhk_card_browser\Entity\CardEntityInterface
   *   The called Card entity.
   */
  public function setPublished($published);

}
