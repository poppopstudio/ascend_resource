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
    // https://www.drupal8.ovh/en/tutoriels/245/custom-views-data-handler-for-a-custom-entity-on-drupal-8
    $data = parent::getViewsData();

    // $data[BASE TABLE of field][TERM FIELD column id]
    $data['resource__category']['category_target_id']['group'] = $this->t('Resource category');
    $data['resource__category']['category_target_id']['title'] = $this->t('Resource has category ID');
    $data['resource__category']['category_target_id']['help'] = $this->t('Resource has the selected (category) taxonomy terms.');

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
