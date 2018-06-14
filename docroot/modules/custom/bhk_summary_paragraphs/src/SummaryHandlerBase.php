<?php

namespace Drupal\bhk_summary_paragraphs;

use Drupal\Component\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Zend\Stdlib\ArrayUtils;
use Drupal\field\Entity\FieldConfig;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Core\Entity\Query\QueryInterface;

/**
 * Base class for Summary Handler plugins.
 */
abstract class SummaryHandlerBase extends PluginBase implements SummaryHandlerInterface {

  use StringTranslationTrait;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a SummaryHandlerBase object.
   *
   * @param array $configuration
   *   Plugin configuration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin Definition.
   * @param \Drupal\Core\Database\Connection $database
   *   Database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getConditionFields() {
    return $this->pluginDefinition['conditionFields'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSortOptions() {
    return isset($this->pluginDefinition['sort']) ? $this->pluginDefinition['sort'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function buildSummary(array &$build, Paragraph $paragraph, ParagraphsType $paragraph_type) {
    // Check for and build directly selected items first.
    $selected_nodes = $this->getDirectSelection($paragraph);

    // Build the query and execute it.
    $query = $this->buildQuery($paragraph, $paragraph_type, $selected_nodes);

    if ($query) {
      $nodes = $query->execute();
    }

    // Return if there's no content to display.
    if (empty($nodes) && empty($selected_nodes)) {

      // Hide title is the summary is empty.
      if (isset($build['field_summary_title'])) {
        $build['field_summary_title']['#access'] = FALSE;
      }
      return;
    }

    // Build a complete list of nodes for the summary.
    if (!empty($nodes)) {
      $summary_nodes = ArrayUtils::merge(
        $selected_nodes,
        $this->entityTypeManager->getStorage('node')->loadMultiple($nodes)
      );
    }
    else {
      $summary_nodes = $selected_nodes;
    }

    // Final access check.
    foreach ($summary_nodes as $key => $node) {
      if (!$node->access('view')) {
        unset($summary_nodes[$key]);
      }
    }

    // Determine view mode to use for rendering summary items.
    $view_mode = $paragraph_type->getThirdPartySetting('bhk_summary_paragraphs', 'summary_view_mode', 'teaser');

    // Render all found nodes and add summary output to paragraph.
    $rendered_nodes = $this->entityTypeManager->getViewBuilder('node')->viewMultiple(
      $summary_nodes,
      $view_mode
    );

    $build['summary_output'] = [
      '#theme' => 'summary_output',
      '#items' => $rendered_nodes,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDirectSelection(Paragraph $paragraph) {
    $nodes = [];

    // Make sure that direct selection has been enabled.
    if ($paragraph->hasField('field_summary_direct') && $paragraph->get('field_summary_direct')->value) {

      // Get total count of summary items to look for.
      if ($paragraph->hasField('field_summary_content')) {
        $nodes = $paragraph->get('field_summary_content')->referencedEntities();
      }
    }

    return $nodes;
  }

  /**
   * {@inheritdoc}
   */
  public function buildQuery(Paragraph $paragraph, ParagraphsType $paragraph_type, array $selected_nodes = []) {
    // Get a list of enabled content types.
    $enabled_content_types = $paragraph_type->getThirdPartySetting('bhk_summary_paragraphs', 'summary_content_types', []);

    if (!$enabled_content_types) {
      return FALSE;
    }

    // Build array of directly selected node ids to exclude.
    $nodes_to_exclude = [];
    if ($selected_nodes) {
      foreach ($selected_nodes as $node) {
        $nodes_to_exclude[] = $node->id();
      }
    }

    // Build a base query.
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', $enabled_content_types, 'IN');

    // Make sure to exclude directly selected items.
    if (!empty($nodes_to_exclude)) {
      $query->condition('nid', $nodes_to_exclude, 'NOT IN');
    }

    // Process the query to add range, sort and additional conditions.
    $this->addFieldBasedConditions($query, $paragraph, $paragraph_type);
    $this->processQuery($query, $paragraph, $selected_nodes);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function processQuery(QueryInterface &$query, Paragraph $paragraph, array $selected_nodes = [], $sort = TRUE) {
    // Get total count of summary items to look for.
    if ($paragraph->hasField('field_summary_count')) {
      $total_count = $paragraph->get('field_summary_count')->value;
    }
    else {
      // Fallback to 3 items in case the summary field was not found.
      $total_count = 3;
    }

    // Adjust total count if there are any directly selected nodes.
    if ($selected_nodes) {
      $total_count = $total_count - count($selected_nodes);
    }

    // Reset the query if all items have been directly selected by the user.
    if ($total_count > 0) {

      if ($sort) {
        $sort_settings = $this->getSortOptions();

        if (empty($sort_settings)) {
          $query->sort('created', 'DESC');
        }
        else {
          foreach ($sort_settings as $field_name => $sort_direction) {
            $query->sort($field_name, $sort_direction);
          }
        }
      }

      $query->range(0, $total_count);
      $query->accessCheck();
    }
    else {
      $query = FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldBasedConditions(QueryInterface &$query, Paragraph $paragraph, ParagraphsType $paragraph_type) {
    $condition_fields = $this->getConditionFields();

    if ($condition_fields) {
      foreach ($condition_fields as $paragraph_field => $node_field) {
        // Sanity checks. Make sure that specified fields exist.
        // Paragraphs check first.
        if (!$paragraph->hasField($paragraph_field)) {
          continue;
        }

        // Check for all selected node types next. Only entity reference fields
        // are allowed here.
        $node_types = $paragraph_type->getThirdPartySetting('bhk_summary_paragraphs', 'summary_content_types', []);
        foreach ($node_types as $node_type) {
          $field_config = FieldConfig::loadByName('node', $node_type, $node_field);

          if (empty($field_config) || (!empty($field_config) && $field_config->getType() !== 'entity_reference')) {
            continue 2;
          }
        }

        // If all checks passed, add the condition to the query.
        $values = [];
        foreach ($paragraph->get($paragraph_field) as $reference_item) {
          $values[] = $reference_item->target_id;
        }

        if ($values) {
          $query->condition($node_field, $values, 'IN');
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateSummaryForm(array &$form, FormStateInterface $form_state) {
    // Verify that at least one content type has been selected for the summary.
    if ($form_state->getValue('enable_summary')) {
      $content_types = $form_state->getValue('summary_content_types', []);

      if (empty($content_types)) {
        $form_state->setErrorByName('summary_content_types', $this->t('You must select at least one content type to use for this summary display.'));
      }
    }

    return TRUE;
  }

}
