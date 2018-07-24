<?php

namespace Drupal\bhk_micro_quiz;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the MicroQuiz entity.
 *
 * @see \Drupal\bhk_micro_quiz\Entity\MicroQuiz.
 */
class MicroQuizAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\bhk_micro_quiz\Entity\MicroQuizEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished microquiz entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published microquiz entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit microquiz entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete microquiz entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add microquiz entities');
  }

}
