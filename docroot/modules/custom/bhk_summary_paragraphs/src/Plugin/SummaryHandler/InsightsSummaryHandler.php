<?php

namespace Drupal\bhk_summary_paragraphs\Plugin\SummaryHandler;

use Drupal\Core\Annotation\Translation;
use Drupal\bhk_summary_paragraphs\Annotation\SummaryHandler;
use Drupal\bhk_summary_paragraphs\SummaryHandlerBase;

/**
 * Provides a resource summary handler.
 *
 * @SummaryHandler(
 *   id = "insights_summary_handler",
 *   label = @Translation("Insights Summary Handler"),
 *   conditionFields = {
 *     "field_insight_type" = "field_insight_type",
 *   }
 * )
 */
class InsightsSummaryHandler extends SummaryHandlerBase {

  // Base class should handle all required logic for this plugin.
}
