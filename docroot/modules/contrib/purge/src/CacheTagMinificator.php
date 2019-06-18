<?php

namespace Drupal\purge;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Cache tag minificator based on static dictionary.
 */
class CacheTagMinificator implements CacheTagMinificatorInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Static dictionary to use for minification.
   *
   * @var array
   */
  protected $dictionary = [];

  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;

    $this->dictionary = $this->moduleHandler->invokeAll('purge_cache_tag_minify_dictionary');
    $this->moduleHandler->alter('purge_cache_tag_minify_dictionary', $this->dictionary);
  }

  /**
   * {@inheritdoc}
   */
  public function minifyCacheTag($cache_tag) {
    return strtr($cache_tag, $this->dictionary);
  }
}
