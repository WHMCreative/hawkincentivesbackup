<?php

namespace Drupal\purge\Plugin\Purge\TagsHeader;

use Drupal\purge\CacheTagMinificatorInterface;
use Drupal\purge\CacheTagsSimplifierInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Base implementation for plugins that add and format a cache tags header.
 */
abstract class TagsHeaderBase extends PluginBase implements TagsHeaderInterface {

  /**
   * The cache tag minificator service.
   *
   * @var \Drupal\purge\CacheTagMinificatorInterface
   */
  protected $cacheTagMinificator;

  /**
   * The cache tags simplifier service.
   *
   * @var \Drupal\purge\CacheTagsSimplifierInterface
   */
  protected $cacheTagsSimplifier;

  /**
   * TagsHeaderBase constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition
   * @param \Drupal\purge\CacheTagMinificatorInterface $cache_tag_mificator
   *   The cache tag minificator service.
   * @param \Drupal\purge\CacheTagsSimplifierInterface $cache_tags_simplifier
   *   The cache tags simplifier service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CacheTagMinificatorInterface $cache_tag_mificator, CacheTagsSimplifierInterface $cache_tags_simplifier) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->cacheTagMinificator = $cache_tag_mificator;
    $this->cacheTagsSimplifier = $cache_tags_simplifier;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('purge.cache_tag_minificator'),
      $container->get('purge.cache_tags_simplifier')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaderName() {
    return $this->getPluginDefinition()['header_name'];
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(array $tags) {
    $this->cacheTagsSimplifier->simplifyCacheTags($tags);
    $tags = array_map([$this->cacheTagMinificator, 'minifyCacheTag'], $tags);
    return implode(' ', $tags);
  }

}
