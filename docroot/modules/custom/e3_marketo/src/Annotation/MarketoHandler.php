<?php

namespace Drupal\e3_marketo\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an annotation object for E3 Marketo Handlers.
 *
 * @see \Drupal\e3_marketo\Plugin\MarketoHandlerManager
 * @see plugin_api
 *
 * @ingroup e3_marketo
 *
 * @Annotation
 */
class MarketoHandler extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * Handler description.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * Applicable bundles for the handler. Empty to apply the handler to all existing bundles.
   *
   * @var array
   */
  public $bundles;

  /**
   * Plugin priority. Higher priority plugins will be executed first.
   *
   * @var int
   */
  public $priority;

}
