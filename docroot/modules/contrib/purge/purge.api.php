<?php

/**
 * @file
 * Documentation of the Purge module.
 */

/**
 * Supply additional dictionary for cache tag minification.
 *
 * Cache tags should be minified because most of webservers and HTTP proxies
 * have an upper limit on HTTP header length. You can supply extra entries into
 * the minification dictionary to gain even shorter cache tags.
 *
 * @return array
 *   Additional entries for the minification dictionary. Keys should be original
 *   substring in the cache tag whereas value should be the replaced value in
 *   the cache tag.
 */
function hook_purge_cache_tag_minify_dictionary() {
  // In this sample we would transform things like:
  // node:1234 => n:1234
  // config:some.config.data => c:some.config.data
  return [
    'node:' => 'n:',
    'config:' => 'c:',
  ];
}

/**
 * Alter the dictionary for cache tag minification.
 *
 * @param array $dictionary
 *   Keys should be original substring in the cache tag whereas value should be
 *   the replaced value in the cache tag.
 */
function hook_purge_cache_tag_minify_dictionary_alter(array &$dictionary) {
  // In this sample we prevent the minification of the "field" substring:
  unset($dictionary['field']);
}

/**
 * Supply additional dictionary for cache tags simplification.
 *
 * Cache tags should be simplified because most of webservers and HTTP proxies
 * have an upper limit on HTTP header length. You can supply extra entries into
 * the simplification dictionary to get even less cache tags.
 *
 * @return array
 *   Additional entries for the simplification dictionary. Keys should be a
 *   list cache tag whereas value should be a regex pattern matching all the
 *   cache tags corresponding to the list cache tag.
 */
function hook_purge_cache_tags_simplify_dictionary() {
  // In this sample we would transform things like:
  // "node:1234, node_list, node:42" => "node_list"
  return [
    'node_list' => '/^node\:/',
  ];
}

/**
 * Alter the dictionary for cache tags simplification.
 *
 * @param array $dictionary
 *   Keys should be a list cache tag whereas value should be a regex pattern
 * matching all the cache tags corresponding to the list cache tag.
 */
function hook_purge_cache_tags_simplify_dictionary_alter(array &$dictionary) {
  // In this sample we prevent the removing of "user:" cache tags when 'user_list'
  // is present.
  unset($dictionary['user_list']);
}
