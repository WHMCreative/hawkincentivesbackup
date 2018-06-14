<?php

namespace Drupal\bhk_summary_paragraphs;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Query\QueryInterface;

/**
 * Base interface for Summary Handler plugins.
 */
interface SummaryHandlerInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Get the name of the Summary Builder plugin.
   *
   * @return string
   *   Label of the plugin.
   */
  public function getLabel();

  /**
   * Get additional conditional fields, if defined by the plugin.
   *
   * @return array
   *   Node fields, keyed by corresponding paragraph fields.
   */
  public function getConditionFields();

  /**
   * Get currently set sorting options for the handler.
   *
   * @return array
   *   Array of sort options.
   */
  public function getSortOptions();

  /**
   * Build a summary component.
   *
   * @param array $build
   *   Build array of the paragraph.
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   Paragraph Entity.
   * @param \Drupal\paragraphs\Entity\ParagraphsType $paragraph_type
   *   Paragraph Type Entity.
   */
  public function buildSummary(array &$build, Paragraph $paragraph, ParagraphsType $paragraph_type);

  /**
   * Extract any directly selected items from the paragraph.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   Paragraph Entity.
   *
   * @return array
   *   Empty array or array of nodes, if extracted.
   */
  public function getDirectSelection(Paragraph $paragraph);

  /**
   * Build the query to use for summary items retrieval.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   Paragraph Entity.
   * @param \Drupal\paragraphs\Entity\ParagraphsType $paragraph_type
   *   Paragraph Type Entity.
   * @param array $selected_nodes
   *   (Optional) Array of directly selected nodes, as returned from
   *   getDirectSelection.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   Compiled query.
   */
  public function buildQuery(Paragraph $paragraph, ParagraphsType $paragraph_type, array $selected_nodes = []);

  /**
   * Process query.
   *
   * Process query to add sort, items limit and perform other final operations
   * before it's executed.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   Current Entity Query.
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   Paragraph Entity.
   * @param array $selected_nodes
   *   (Optional) Array of directly selected nodes, as returned from
   *   getDirectSelection.
   * @param bool $sort
   *   (Optional) Set to FALSE to disable default sorting, useful in cases when
   *   plugins want to implement their own sort logic. Default sorts by items
   *   'created' date.
   */
  public function processQuery(QueryInterface &$query, Paragraph $paragraph, array $selected_nodes = [], $sort = TRUE);

  /**
   * Extract additional entity reference conditions.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   Current Entity Query.
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   Paragraph Entity.
   * @param \Drupal\paragraphs\Entity\ParagraphsType $paragraph_type
   *   Paragraph Type Entity.
   */
  public function addFieldBasedConditions(QueryInterface &$query, Paragraph $paragraph, ParagraphsType $paragraph_type);

  /**
   * Summary plugin validation handler.
   *
   * Validation handler, used to validate the paragraph type's edit form when
   * the plugin has been selected. This allows plugins to implement their own
   * validation checks before they can be selected.
   *
   * @param array $form
   *   Paragraph Type's edit form.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form State.
   */
  public function validateSummaryForm(array &$form, FormStateInterface $form_state);

}
