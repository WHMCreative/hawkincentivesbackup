<?php

namespace Drupal\bhk_micro_quiz;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of MicroQuiz entities.
 *
 * @ingroup bhk_micro_quiz
 */
class MicroQuizListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('MicroQuiz ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.micro_quiz.edit_form',
      ['micro_quiz' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
