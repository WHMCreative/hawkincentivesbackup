<?php

namespace Drupal\bhk_micro_quiz\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Ajax command to reload the current page.
 *
 * @package Drupal\bhk_micro_quiz\Ajax
 */
class ReloadPageAjaxCommand implements CommandInterface {

  /**
   * Render ajax command to reload the current page.
   *
   * @return array
   */
  public function render() {
    return [
      'command' => 'reload',
    ];
  }

}
