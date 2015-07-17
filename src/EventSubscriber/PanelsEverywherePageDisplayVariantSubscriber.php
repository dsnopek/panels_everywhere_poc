<?php

/**
 * @file
 * Contains \Drupal\panels_everywhere_poc\EventSubscriber\PanelsEverywherePageDisplayVariantSubscriber.
 */

namespace Drupal\panels_everywhere_poc\EventSubscriber;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Drupal\Core\Display\PageVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Selects the appropriate page display variant from 'site_template'.
 */
class PanelsEverywherePageDisplayVariantSubscriber implements EventSubscriberInterface {
  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Constructs a new PageManagerRoutes.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityStorage = $entity_manager->getStorage('page');
  }

  /**
   * Selects the page display variant.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
    $page = $this->entityStorage->load('site_template');
    if ($variant = $page->getExecutable()->selectDisplayVariant()) {
      if ($variant instanceof PageVariantInterface) {
        // This is the most important bit: telling core what variant to use.
        // @todo: This won't actually work until we solve issue #2511570, or by
        // commenting out the loop in PanelsDisplayVariant::getContextAsTokenData()
        $event->setPluginId($variant->getPluginId());
        $event->setPluginConfiguration($variant->getConfiguration());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[RenderEvents::SELECT_PAGE_DISPLAY_VARIANT][] = array('onSelectPageDisplayVariant');
    return $events;
  }

}
