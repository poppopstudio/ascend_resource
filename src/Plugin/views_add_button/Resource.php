<?php

namespace Drupal\ascend_resource\Plugin\views_add_button;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\views_add_button\Plugin\views_add_button\ViewsAddButtonDefault;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TODO: class docs.
 *
 * @ViewsAddButton(
 *   id = "ascend_resource_resource",
 *   label = @Translation("Resource"),
 *   category = @Translation("TODO: replace this with a value"),
 * )
 */
class Resource extends ViewsAddButtonDefault implements ContainerFactoryPluginInterface {

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
    );
  }

  /**
   * Creates a Resource instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current active user.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function description() {
    return $this->t('Default Views Add Button URL Generator for entitites which do not have a dedicated ViewsAddButton plugin');
  }

  /**
   * {@inheritdoc}
   */
  public static function generateUrl($entity_type, $bundle, array $options, $context = '') {

    // $u = $entity_type === $bundle ? '/' . $entity_type . '/add' : '/' . $entity_type . '/add/' . $bundle;

    return Url::fromUserInput($u, $options);
  }
}
