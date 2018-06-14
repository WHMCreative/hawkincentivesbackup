<?php

namespace Drupal\e3_marketo;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Marketo form entity entity.
 *
 * @see \Drupal\e3_marketo\Entity\MarketoFormEntity.
 */
class MarketoFormEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished marketo form entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published marketo form entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit marketo form entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete marketo form entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add marketo form entity entities');
  }

}
