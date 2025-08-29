<?php

namespace Drupal\ascend_resource\Hook;

use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\taxonomy\Entity\Term;

/**
 * Contains hook implementations for the Ascend resource module.
 */
class ResourceHooks {

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(&$form, FormStateInterface $form_state, $form_id) {
    return;
  }

  /**
   * Implements hook_entity_view_alter().
   */
  #[Hook('entity_view_alter')]
  public function entityViewAlter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
    /** For Category taxonomy terms.
     *  - show the Resources embed view for leaf terms.
     *  - show the Category children embed view for node terms.
     */

    if (
      $entity->getEntityTypeId() === 'taxonomy_term'
      && $entity->bundle() === 'category'
      && $display->getMode() == 'default'
    ) {  // NB!

      $term_id = (int) $entity->id();

      // Display the Category's children terms.
      if ($this->termHasChildren($term_id)) {
        $build['category_child_terms'] = views_embed_view('category_child_terms', 'embed_1', $term_id);
        $build['category_child_terms']['#weight'] = 25;
      }

      // Display the Category's resources view.
      else {
        $build['category_resources'] = views_embed_view('category_resources', 'embed_1', $term_id);
        $build['category_resources']['#weight'] = 25;
      }
    }
  }

  protected function termHasChildren(int $tid) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('parent', $tid)
      ->accessCheck(TRUE)
      ->count(); // We only need to know if there's some, or none.
    return $query->execute();
  }

  /**
   * Implements hook_ENTITY_TYPE_presave().
   */
  #[Hook('taxonomy_term_presave')]
  public function taxonomyTermPresave(EntityInterface $entity) {
    // Base field overrides required to set text_format, BUT...
    // They don't work without the UI, so we have to force at save (import) time.
    if ($entity->bundle() === 'category') {
      $description = $entity->description;
      if (!empty($description->value) && empty($description->format)) {
        $description->format = 'plain_text';
      }
    }
  }

  /**
   * Implements hook_tokens_alter().
   */
  #[Hook('tokens_alter')]
  public function tokensAlter(array &$replacements, array $context, BubbleableMetadata $bubbleable_metadata) {
    // Convert term description token to string and strip html tags.
    // Without this, term desc is wrapped in a <p> tag.
    if ($context['type'] == 'term') {
      if (isset($replacements['[term:description]'])) {
        $desc = (string) $replacements['[term:description]'];
        $desc = strip_tags($desc);
        $replacements['[term:description]'] = $desc;
      }
    }
  }

  /**
   * Implements hook_entity_form_display_alter
   */
  #[Hook('entity_form_display_alter')]
  public function entityFormDisplayAlter(EntityFormDisplayInterface $form_display, array $context) {

    // If I tweak this in the future this is how to switch off readonly for edit forms.
    // We might want this if there needed to be a perm for 'edit category'.
    // ** TWEAKED (uncommented) **
    if ($context['form_mode'] === 'edit') {
      return;
    }

    $types = ['resource', ];

    // Change the category field to a readonly widget if ?cid is a known term.
    // if ($context['entity_type'] == 'node' && in_array($context['bundle'], $types)) {
    if (in_array($context['entity_type'], $types)) {

      $category_id = \Drupal::request()->get('cid');
      if (!isset($category_id) || !is_numeric($category_id)) {
        return;
      }

      $category_term = Term::load($category_id);
      if (!$category_term instanceof Term || !($category_term->bundle() === 'category')) {
        return;
      }

      $component = $form_display->getComponent('category'); // field name

      if ($component) {
        $component['type'] = 'readonly_field_widget';
        $component['settings'] = [
          'label' => 'inline',
          'formatter_type' => 'cshs_full_hierarchy',
          // 'formatter_settings' => [
          //   // 'entity_reference_label' => ['link' => FALSE],
          //   'clear' => TRUE,
          //   'format' => "[term:parents:join: » ] » [term:description]",
          // ]
        ];

        $form_display->setComponent('category', $component);
      }
    }
  }
}
