<?php

namespace Drupal\bhk_views_filter\Plugin\views\filter;

use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;

/**
 * Filters by given list of authored on years options.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("years_date_only")
 */
class AuthoredYears extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {

    parent::init($view, $display, $options);

    $this->valueTitle = t('Allowed years');
    $this->valueOptions = $this->generateOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $table = explode('_', $this->table)[0];
    if (!empty($this->value)) {
      $start_date = strtotime($this->value[0] . '-01-01 00:00:0');
      $end_date = strtotime(($this->value[0] + 1) . '-01-01 00:00:0');
      $snippet = $table . "_field_data.created BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
      $this->query->addWhereExpression("where", $snippet);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    if (!empty($this->value)) {
      parent::validate();
    }
  }

  /**
   * Helper function that generates the options.
   *
   * @return array
   *   - Years.
   */
  public function generateOptions() {
    // Array keys are used to compare with the table field values.
    return $this->getYears();
  }

  /**
   * Helper function that gets years for published content type.
   *
   * @return array
   *   - Unique Years.
   */
  public function getYears() {
    $connection = \Drupal::database();
    $viewId = $this->view->id();
    $dates = [];

    if ($viewId === 'news') {
      $contentType = 'news';

      // field_resource_type_target_id = 1 is Newsletters.
      $query = $connection->select('node_field_data', 'nfd')->distinct();
      //$query->leftJoin('node__field_resource_type', 'nfrt', 'nfd.nid=nfrt.entity_id');
      $query
        ->condition('nfd.type', $contentType, '=')
        ->condition('nfd.status', 1, '=')
        //->condition('nfrt.field_resource_type_target_id', 1, '=')
        ->fields('nfd', ['created']);

      $dates = $query->execute()->fetchAll();
    }

    $years = FALSE;
    $unique = [];

    // Transform date to timestamp and transform to year.
    if ($dates) {
      foreach ($dates as $date) {
        $years[] = date('Y', $date->created);
      }
      $unique = array_combine(array_unique($years), array_unique($years));
      arsort($unique);
    }

    return $unique;
  }

}
