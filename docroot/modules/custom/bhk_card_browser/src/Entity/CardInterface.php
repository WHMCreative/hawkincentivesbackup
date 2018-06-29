<?php

namespace Drupal\bhk_card_browser\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Card entities.
 *
 * @ingroup bhk_card_browser
 */
interface CardInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

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
   * @return \Drupal\bhk_card_browser\Entity\CardInterface
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
   * @return \Drupal\bhk_card_browser\Entity\CardInterface
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
   * @return \Drupal\bhk_card_browser\Entity\CardInterface
   *   The called Card entity.
   */
  public function setPublished($published);

  /**
   * Gets the Card revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Card revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\bhk_card_browser\Entity\CardInterface
   *   The called Card entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Card revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Card revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\bhk_card_browser\Entity\CardInterface
   *   The called Card entity.
   */
  public function setRevisionUserId($uid);

}
