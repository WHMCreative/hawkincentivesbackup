<?php

namespace Drupal\bhk_micro_quiz\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining MicroQuiz entities.
 *
 * @ingroup bhk_micro_quiz
 */
interface MicroQuizEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the MicroQuiz name.
   *
   * @return string
   *   Name of the MicroQuiz.
   */
  public function getName();

  /**
   * Sets the MicroQuiz name.
   *
   * @param string $name
   *   The MicroQuiz name.
   *
   * @return \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface
   *   The called MicroQuiz entity.
   */
  public function setName($name);

  /**
   * Gets the MicroQuiz creation timestamp.
   *
   * @return int
   *   Creation timestamp of the MicroQuiz.
   */
  public function getCreatedTime();

  /**
   * Sets the MicroQuiz creation timestamp.
   *
   * @param int $timestamp
   *   The MicroQuiz creation timestamp.
   *
   * @return \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface
   *   The called MicroQuiz entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the MicroQuiz published status indicator.
   *
   * Unpublished MicroQuiz are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the MicroQuiz is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a MicroQuiz.
   *
   * @param bool $published
   *   TRUE to set this MicroQuiz to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface
   *   The called MicroQuiz entity.
   */
  public function setPublished($published);

}
