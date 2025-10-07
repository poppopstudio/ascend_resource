<?php

namespace Drupal\ascend_resource\Entity\Handler;

use Drupal\views\EntityViewsData;

/**
 * Provides the Views data handler for the Resource entity.
 */
class ResourceViewsData extends EntityViewsData {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Add a data handler for filter.
    $data['resource__category']['category_target_id']['filter'] =
      [
        'title' => $this->t('Resource has taxonomy term'),
        'id' => 'taxonomy_index_tid',
        'field' => 'category_target_id',
        'numeric' => TRUE,
        'allow empty' => TRUE,
      ];

    return $data;
  }
}
