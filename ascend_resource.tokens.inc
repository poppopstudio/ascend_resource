<?php

/**
 * @file
 * Provide token hooks for resource.
 */

use Drupal\ascend_resource\Hook\ResourceHooks;
use Drupal\Core\Hook\LegacyHook;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Legacy hook implementation.
 *
 * @todo Remove this method when support for Drupal core < 11.1 is dropped.
 */
#[LegacyHook]
function ascend_resource_tokens_alter(array &$replacements, array $context, BubbleableMetadata $bubbleable_metadata) {
  \Drupal::service(ResourceHooks::class)->tokensAlter($replacements, $context, $bubbleable_metadata);
}
