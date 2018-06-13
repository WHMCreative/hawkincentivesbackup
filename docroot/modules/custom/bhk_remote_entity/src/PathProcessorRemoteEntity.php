<?php

namespace Drupal\bhk_remote_entity;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Processes the inbound path using path alias lookups.
 */
class PathProcessorRemoteEntity implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  /**
   * Constructs a PathProcessorAlias object.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    return $path;
  }

  /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {
    if (isset($options['route']) && isset($options['entity'])) {
      if ($options['route']->getPath() == '/node/{node}') {
        switch ($options['entity']->bundle()) {
          // Add cases for each content type and condition and set `base_url`.
          case 'event':
            if (!$options['entity']->field_external_link->isEmpty()) {
              $url = Url::fromUri($options['entity']->field_external_link->first()->getValue()['uri']);
              $options['base_url'] = $url->toString();
              return '';
            }
            break;
        }
      }
    }
    return $path;
  }

}
