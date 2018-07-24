<?php

/**
 * @file
 * Add filter to view.
 */

/**
 * Implements hook_views_data_alter().
 */
function bhk_views_filter_views_data_alter(array &$data) {

  $data['node_field_data']['authored_year'] = [
    'title' => t('Authored on Year'),
    'filter' => [
      'title' => t('Authored on Year'),
      'help' => t('Filtering nodes by authored on year.'),
      'field' => 'created',
      'id' => 'years_date_only',
    ],
  ];

}
