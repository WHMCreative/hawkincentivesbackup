<?php

namespace Drupal\purge;

/**
 * Interface of a cache tag minificator.
 *
 * There is an upper limit in most web-servers / proxies on HTTP header length.
 * Thus we minify cache tags to fit as much as possible into the imposed limit.
 */
interface CacheTagMinificatorInterface {

  /**
   * Minify a given cache tag.
   *
   * Minification aims at reducing tag size based on a predefined mapping.
   * For example `node:1` is `n:1` minified. Other modules can extend the
   * mappings by implementing `hook_purge_cache_tag_minify_dictionary`.
   *
   * @see hook_purge_cache_tag_minify_dictionary()
   *
   * @param string $cache_tag
   *   Cache tag to minify.
   *
   * @return string
   *   Minified cache tag.
   */
  public function minifyCacheTag($cache_tag);

}
