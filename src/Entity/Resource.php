<?php

namespace Drupal\ascend_resource\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Provides the Resource entity.
 *
 * @ContentEntityType(
 *   id = "resource",
 *   label = @Translation("Resource"),
 *   label_collection = @Translation("Resources"),
 *   label_singular = @Translation("resource"),
 *   label_plural = @Translation("resources"),
 *   label_count = @PluralTranslation(
 *     singular = "@count resource",
 *     plural = "@count resources",
 *   ),
 *   base_table = "resource",
 *   data_table = "resource_field_data",
 *   revision_table = "resource_revision",
 *   revision_data_table = "resource_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = "TRUE",
 *   collection_permission = "access resource overview",
 *   handlers = {
 *     "access" = "Drupal\entity\EntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\entity_admin_handlers\SingleBundleEntity\SingleBundleEntityHtmlRouteProvider",
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *     "form" = {
 *       "default" = "Drupal\ascend_resource\Form\ResourceForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "list_builder" = "Drupal\ascend_resource\Entity\Handler\ResourceListBuilder",
 *     "views_data" = "Drupal\ascend_resource\Entity\Handler\ResourceViewsData",
 *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
 *   },
 *   admin_permission = "administer resource entities",
 *   entity_keys = {
 *     "id" = "resource_id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "owner" = "uid",
 *     "uid" = "uid",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   field_ui_base_route = "entity.resource.field_ui_base",
 *   links = {
 *     "add-form" = "/resource/add",
 *     "canonical" = "/resource/{resource}",
 *     "collection" = "/admin/content/resource",
 *     "delete-form" = "/resource/{resource}/delete",
 *     "edit-form" = "/resource/{resource}/edit",
 *     "field-ui-base" = "/admin/structure/resource",
 *     "version-history" = "/admin/structure/resource/{resource}/revisions",
 *     "revision" = "/admin/structure/resource/{resource}/revisions/{resource_revision}/view",
 *     "revision-revert-form" = "/admin/structure/resource/{resource}/revisions/{resource_revision}/revert",
 *     "revision-delete-form" = "/admin/structure/resource/{resource}/revisions/{resource_revision}/delete",
 *     "translation_revert" = "/admin/structure/resource/{resource}/revisions/{resource_revision}/revert/{langcode}",
 *   },
 * )
 */
class Resource extends EditorialContentEntityBase implements ResourceInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Resource title"))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', ['label' => 'hidden', 'type' => 'string', 'weight' => -5])
      ->setDisplayOptions('form', ['type' => 'string_textfield', 'weight' => -5])
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid']
      ->setLabel(t('Authored by'))
      ->setDescription(t('The username of the content author.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']
      ->setLabel(t("Published"))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 120,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t("Authored on"))
      ->setDescription(t("The date & time that the resource was created."))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t("Changed"))
      ->setDescription(t("The time that the resource was last edited."))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['category'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t("Category"))
      ->setDescription(t("The resource's category."))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE)
      ->setCardinality(-1)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default:taxonomy_term')
      ->setSetting("handler_settings", [
        'target_bundles' => [
          'category' => 'category',
        ],
        'sort' => [
          'field' => 'name',
          'direction' => 'asc',
        ],
        'auto_create' => FALSE,
      ])
      ->setDisplayOptions('form', [
        'type' => 'cshs',
        'weight' => -10,
        'settings' => [
          'force_deepest' => TRUE,
          'parent' => 0,
          'none_label' => ' - Select category - ',
        ]
      ])
      ->setDisplayOptions('view', [
        'type' => 'cshs_flexible_hierarchy',
        'label' => 'inline',
        'weight' => -10,
        'settings' => [
          'format' => '[term:parents:join: » ] » [term:description]',
          'link' => FALSE,
          'clear' => TRUE,
        ]
      ]);

    return $fields;
  }

  // protected function getNewRevisionDefault() {
  //   return TRUE;
  // }
}
