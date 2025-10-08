<?php

namespace Drupal\ascend_resource\Hook;

use Drupal\Core\Cache\CacheableMetadata;
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
    // \Drupal::messenger()->addMessage(t("Form ID: @fid", ['@fid' => $form_id]));
    return;
  }


  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_taxonomy_term_category_edit_info_form_alter')]
  public function formTaxonomyTermCategoryEditInfoFormAlter(&$form, FormStateInterface $form_state, $form_id) {
    // Hide taxonomy term relations element; don't change in this form mode.
    $form['relations']['#access'] = FALSE;
    return;
  }

  /**
   * Implements hook_entity_view_alter().
   */
  #[Hook('entity_view_alter')]
  public function entityViewAlter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
    /**
     * For Category taxonomy terms in 'default' view mode.
     *  - show the Resources embed view for leaf terms.
     *  - show the Category children embed view for node terms.
     */

    if (
      $entity->getEntityTypeId() === 'taxonomy_term'
      && $entity->bundle() === 'category'
      && $display->getMode() == 'default'
    ) {

      $term_id = (int) $entity->id();

      $additional_cacheable_metadata = new CacheableMetadata();

      // Display the Category's children terms.
      if ($this->termHasChildren($term_id)) {
        $build['category_child_terms'] = views_embed_view('category_child_terms', 'embed_1', $term_id);
        $build['category_child_terms']['#weight'] = 25;

        $additional_cacheable_metadata->addCacheTags(['taxonomy_term_list']);
      }

      // Display the Category's resources view.
      else {
        $build['category_resources'] = views_embed_view('category_resources', 'embed_1', $term_id);
        $build['category_resources']['#weight'] = 25;

        $additional_cacheable_metadata->addCacheTags(['resource_list']);
      }

      $additional_cacheable_metadata->applyTo($build);
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
    /**
     * Base field overrides required to set description text_format, BUT...
     * They don't work without the UI, so we have to force at save (import) time.
     * Might want to enforce for all vocabs not just category?
     */
    if ($entity->bundle() === 'category') {
      $description = $entity->description;
      if (!empty($description->value) && empty($description->format)) {
        $description->format = 'plain_text';
      }
    }
  }


  /**
   * Implements hook_entity_form_display_alter
   */
  #[Hook('entity_form_display_alter')]
  public function entityFormDisplayAlter(EntityFormDisplayInterface $form_display, array $context) {

    // Only adjust the widget if in Add mode.
    if ($context['form_mode'] === 'edit') {
      return;
    }

    // Change the category field to a readonly widget...
    if ($context['entity_type'] === 'resource') {

      // ...if ?cid is set and numeric.
      $category_id = \Drupal::request()->get('cid');
      if (!isset($category_id) || !is_numeric($category_id)) {
        return;
      }

      // ...if ?cid is a legit category term.
      $category_term = Term::load($category_id);
      if (!$category_term instanceof Term || !($category_term->bundle() === 'category')) {
        return;
      }

      $component = $form_display->getComponent('category'); // field name
      if ($component) {
        $component['type'] = 'readonly_field_widget';
        $component['settings'] = [
          'formatter_type' => 'cshs_full_hierarchy',
        ];
        $form_display->setComponent('category', $component);
      }
    }
  }


  /* These two functions need to be in a base module ideally! */

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
   * Implements hook_auto_username_alter().
   */
  #[Hook('auto_username_alter')]
  public function autoUsernameAlter(array &$data): void {
    // Force usernames to be all lower case.
    // $data['username'] = strtolower($data['username']);
  }

}
