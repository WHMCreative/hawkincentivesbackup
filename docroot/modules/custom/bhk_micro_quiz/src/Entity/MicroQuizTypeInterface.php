<?php

namespace Drupal\bhk_micro_quiz\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining MicroQuiz bundle entities.
 */
interface MicroQuizTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the description of the entity.
   *
   * @return string|null
   *   Description of the entity, or NULL if there is no description defined.
   */
  public function getDescription();

}
