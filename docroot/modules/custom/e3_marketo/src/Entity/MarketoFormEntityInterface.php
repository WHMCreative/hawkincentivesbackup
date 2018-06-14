<?php

namespace Drupal\e3_marketo\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Marketo form entity entities.
 *
 * @ingroup e3_marketo
 */
interface MarketoFormEntityInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Marketo form entity name.
   *
   * @return string
   *   Name of the Marketo form entity.
   */
  public function getName();

  /**
   * Sets the Marketo form entity name.
   *
   * @param string $name
   *   The Marketo form entity name.
   *
   * @return \Drupal\e3_marketo\Entity\MarketoFormEntityInterface
   *   The called Marketo form entity entity.
   */
  public function setName($name);

  /**
   * Gets the Marketo form entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Marketo form entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Marketo form entity creation timestamp.
   *
   * @param int $timestamp
   *   The Marketo form entity creation timestamp.
   *
   * @return \Drupal\e3_marketo\Entity\MarketoFormEntityInterface
   *   The called Marketo form entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Marketo form entity published status indicator.
   *
   * Unpublished Marketo form entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Marketo form entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Marketo form entity.
   *
   * @param bool $published
   *   TRUE to set this Marketo form entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\e3_marketo\Entity\MarketoFormEntityInterface
   *   The called Marketo form entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Marketo form entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Marketo form entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\e3_marketo\Entity\MarketoFormEntityInterface
   *   The called Marketo form entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Marketo form entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Marketo form entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\e3_marketo\Entity\MarketoFormEntityInterface
   *   The called Marketo form entity entity.
   */
  public function setRevisionUserId($uid);

  /**
   * Retrieve MArketo Form ID.
   *
   * @return string|NULL
   *   Form ID if set, NULL if empty.
   */
  public function getFormId();

}
