<?php

namespace Drupal\bhk_micro_quiz;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Defines the storage handler class for MicroQuiz entities.
 *
 * This extends the base storage class, adding required special handling for
 * MicroQuiz entities.
 *
 * @ingroup bhk_micro_quiz
 */
class MicroQuizStorage extends SqlContentEntityStorage implements MicroQuizStorageInterface {

}
