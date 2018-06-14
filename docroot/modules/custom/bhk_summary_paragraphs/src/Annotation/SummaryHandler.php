<?php

namespace Drupal\bhk_summary_paragraphs\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an annotation object for Summary Paragraph Handlers.
 *
 * @see \Drupal\bhk_summary_paragraphs\SummaryHandlerManager
 * @see plugin_api
 *
 * @Annotation
 */
class SummaryHandler extends Plugin {

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
   * Array of additional fields to use for condition checks.
   *
   * Array format:
   * - key - paragraph field.
   * - value - corresponding node entity reference field.
   *
   * @var array
   */
  public $conditionFields;

  /**
   * Array of the sorting options.
   *
   * If not set, items will be sorted by created date.
   * Array format:
   * - key - content field to sort on.
   * - value - sort direction, either "DESC" or "ASC".
   *
   * @var array
   */
  public $sort;

}
