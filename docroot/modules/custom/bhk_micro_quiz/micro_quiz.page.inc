<?php

/**
 * @file
 * Contains micro_quiz.page.inc.
 *
 * Page callback for MicroQuiz entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for MicroQuiz templates.
 *
 * Default template: micro_quiz.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_micro_quiz(array &$variables) {

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
