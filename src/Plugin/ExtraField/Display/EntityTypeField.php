<?php

namespace Drupal\ascend_resource\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayFormattedBase;

/**
 * Example Extra field with formatted output.
 *
 * @ExtraFieldDisplay(
 *   id = "ascend_entity_type_field",
 *   label = @Translation("Entity type"),
 *   description = @Translation("An extra field to display entity type formatted as field with label."),
 *   bundles = {
 *     "node.page",
 *     "resource.resource",
 *     "taxonomy_term.category"
 *   }
 * )
 */


class EntityTypeField extends ExtraFieldDisplayFormattedBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->t('Entity type');
  }

  /**
   * {@inheritdoc}
   */
  public function getLabelDisplay() {
    return 'hidden';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(ContentEntityInterface $entity) {
    return [
      ['#markup' => $entity->bundle()],
    ];
  }
}
