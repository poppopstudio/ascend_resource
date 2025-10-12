<?php

namespace Drupal\ascend_resource\Plugin\pathauto\AliasType;

use Drupal\pathauto\Plugin\pathauto\AliasType\EntityAliasTypeBase;

/**
 * Provides an alias type for Resource entities.
 *
 * @AliasType(
 *   id = "resource",
 *   label = @Translation("Resource"),
 *   types = {"resource"},
 *   provider = "ascend_resource",
 *   context_definitions = {
 *     "resource" = @ContextDefinition("entity:resource", label = @Translation("Resource"))
 *   }
 * )
 */
class ResourceAliasType extends EntityAliasTypeBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntityTypeId() {
    return 'resource';
  }
}
