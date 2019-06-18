<?php

namespace Drupal\purge;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Cache tags simplifier based on static dictionary of list cache tags.
 *
 * @todo Incorporate: https://www.drupal.org/project/drupal/issues/2145751.
 */
class CacheTagsSimplifier implements CacheTagsSimplifierInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Static dictionary to use for simplification.
   *
   * @var array
   */
  protected $dictionary = [];

  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;

    $this->dictionary = $this->moduleHandler->invokeAll('purge_cache_tags_simplify_dictionary');
    $this->moduleHandler->alter('purge_cache_tags_simplify_dictionary', $this->dictionary);
  }

  /**
   * {@inheritdoc}
   */
  public function simplifyCacheTags(array &$cache_tags) {
    foreach ($this->dictionary as $list_tag => $pattern) {
      if (in_array($list_tag, $cache_tags)) {
        foreach ($cache_tags as $key => $tag) {
          if (preg_match($pattern, $tag)) {
            unset($cache_tags[$key]);
          }
        }
      }
    }
  }
}
