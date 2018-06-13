<?php

namespace Drupal\bhk_summary_paragraphs\Plugin\SummaryHandler;

use Drupal\Core\Annotation\Translation;
use Drupal\bhk_summary_paragraphs\Annotation\SummaryHandler;
use Drupal\bhk_summary_paragraphs\SummaryHandlerBase;

/**
 * Provides a default version of summary handler.
 *
 * @SummaryHandler(
 *   id = "default_summary_handler",
 *   label = @Translation("Default Summary Handler"),
 *   conditionFields = {},
 * )
 */
class DefaultSummaryHandler extends SummaryHandlerBase {

  // Base class should handle all required logic for this plugin.
}
