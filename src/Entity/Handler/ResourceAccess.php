<?php

namespace Drupal\ascend_resource\Entity\Handler;

use Drupal\Core\Access\AccessResult;
use Drupal\entity\EntityAccessControlHandler;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides the access handler for the Resource entity.
 */
class ResourceAccess extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkFieldAccess($operation, FieldDefinitionInterface $field_definition, AccountInterface $account, ?FieldItemListInterface $items = NULL) {
    if ($operation == 'edit' && $field_definition->getName() == 'created' || $field_definition->getName() == 'uid') {
      if (!$account->hasPermission('administer resource entities')) {
        return AccessResult::forbidden()->addCacheableDependency($account);
      }
    }

    return parent::checkFieldAccess($operation, $field_definition, $account, $items);
  }

}
