<?php

namespace Drupal\ascend_resource\EventSubscriber;

use Drupal\flag\Event\FlagEvents;
use Drupal\flag\Event\FlaggingEvent;
use Drupal\flag\Event\UnflaggingEvent;
use Drupal\Core\Cache\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Invalidates cache tags when entities are flagged or unflagged.
 */
class FlagCacheInvalidationSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[FlagEvents::ENTITY_FLAGGED][] = ['onFlag'];
    $events[FlagEvents::ENTITY_UNFLAGGED][] = ['onUnflag'];
    return $events;
  }

  /**
   * Responds to entity flagged event.
   *
   * @param \Drupal\flag\Event\FlaggingEvent $event
   *   The flagging event.
   */
  public function onFlag(FlaggingEvent $event) {
    $this->invalidateEntityCache($event);
  }

  /**
   * Responds to entity unflagged event.
   *
   * @param \Drupal\flag\Event\UnflaggingEvent $event
   *   The unflagging event.
   */
  public function onUnflag(UnflaggingEvent $event) {
    $this->invalidateEntityCache($event);
  }

  /**
   * Invalidates cache tags for the flagged entity.
   *
   * @param \Drupal\flag\Event\FlaggingEvent|\Drupal\flag\Event\UnflaggingEvent $event
   *   The flagging or unflagging event.
   */
  protected function invalidateEntityCache($event) {
    if ($event instanceof UnflaggingEvent) {

      // Unflagging event returns an array.
      $flaggings = $event->getFlaggings();

      if (empty($flaggings)) {
        return;
      }

      $flagging = reset($flaggings);
    }
    else {
      // Flagging event returns only a singleton.
      $flagging = $event->getFlagging();
    }

    // Get flagged entity details.
    $entity_type = $flagging->getFlaggableType();
    $entity_id = $flagging->getFlaggableId();

    // Invalidate the specific entity's cache tag.
    $cache_tag = $entity_type . ':' . $entity_id;
    Cache::invalidateTags([$cache_tag]);
  }

}
