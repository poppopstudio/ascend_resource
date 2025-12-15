<?php

namespace Drupal\ascend_resource\Entity\Handler;

use Drupal\entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides the access handler for the Resource entity.
 */
class ResourceAccess extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    // Handle revision operations.
    if (in_array($operation, ['view revision', 'view all revisions'])) {
      return AccessResult::allowedIfHasPermission($account, 'view resource revisions')
        ->cachePerPermissions();
    }

    if (in_array($operation, ['revert', 'revert revision'])) {
      return AccessResult::allowedIfHasPermission($account, 'revert resource revisions')
        ->cachePerPermissions();
    }

    if ($operation === 'delete revision') {
      return AccessResult::allowedIfHasPermission($account, 'delete resource revisions')
        ->cachePerPermissions();
    }

    // For all other operations, use parent EntityAccessControlHandler logic
    return parent::checkAccess($entity, $operation, $account);
  }

}
