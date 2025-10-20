<?php

namespace Drupal\ascend_resource\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the default form handler for the Resource entity.
 */
class ResourceForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\ascend_resource\Entity\Resource $resourcep */
    $resource = $this->entity;

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('<em>Edit @type</em> @title', [
        '@type' => 'resource',
        '@title' => $resource->label(),
      ]);
    }

    // Emulates entity info behaviour similar to nodes (guess where it's from).
    $form['meta'] = [
      '#type' => 'details',
      '#group' => 'advanced',
      '#weight' => -100,
      '#title' => $this->t('Status'),
      '#attributes' => ['class' => ['entity-meta__header']],
      '#tree' => TRUE,
      '#access' => $this->currentUser()->hasPermission('update any resource'),
    ];
    $form['meta']['published'] = [
      '#type' => 'item',
      '#markup' => $resource->isPublished() ? $this->t('Published') : $this->t('Not published'),
      // This line seems redundant but the above line doesn't work anyway? Only shows published for either.
      '#access' => !$resource->isNew(),
      '#wrapper_attributes' => ['class' => ['entity-meta__title']],
    ];
    $form['meta']['changed'] = [
      '#type' => 'item',
      '#title' => $this->t('Last saved'),
      // '#markup' => !$resource->isNew() ? $this->dateFormatter->format($resource->getChangedTime(), 'short') : $this->t('Not saved yet'),
      '#markup' => !$resource->isNew() ? \Drupal::service('date.formatter')->format($resource->getChangedTime(), 'short') : $this->t('Not saved yet'),
      '#wrapper_attributes' => ['class' => ['entity-meta__last-saved']],
    ];
    $form['meta']['author'] = [
      '#type' => 'item',
      '#title' => $this->t('Author'),
      '#markup' => $resource->getOwner()?->getAccountName() ?? 'n/a!',
      '#wrapper_attributes' => ['class' => ['entity-meta__author']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $saved = parent::save($form, $form_state);
    $form_state->setRedirectUrl($this->entity->toUrl('canonical'));

    return $saved;
  }

}
