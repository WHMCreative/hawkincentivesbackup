<?php

namespace Drupal\purge;

/**
 * Interface of a cache tags simplifier.
 *
 * There is an upper limit in most web-servers / proxies on HTTP header length.
 * Thus we simplify cache tags to fit as much as possible into the imposed limit.
 * For this we remove individual cache tags when the corresponding list cache
 * tag is present.
 *
 * @see https://www.drupal.org/project/drupal/issues/3001276
 */
interface CacheTagsSimplifierInterface {

  /**
   * Simplify a given list of cache tags.
   *
   * Simplification aims at reducing redundant cache tags from the list of
   * tags. For example we have a view that shows 5 nodes and adds all of their
   * tags and also node_list tag. The result here should drop all concrete
   * `node:id` tags, as they are already covered by the main list one.
   *
   * Note that this is not limited for nodes or list tags only. Custom code can
   * consider other redundant scenarios and add them through implementing hook
   * `hook_purge_cache_tags_simplify_dictionary` implementation.
   *
   * @see hook_purge_cache_tags_simplify_dictionary()
   *
   * @param string[] $cache_tags
   *   Cache tags to simplify.
   */
  public function simplifyCacheTags(array &$cache_tags);

}
