<?php

namespace Drupal\bhk_summary_paragraphs;

use Drupal\paragraphs\ParagraphViewBuilder as CoreParagraphViewBuilder;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Theme\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Pragraphs view builder override to add summary-related logic.
 */
class ParagraphViewBuilder extends CoreParagraphViewBuilder {

  /**
   * Summary handler manager.
   *
   * @var \Drupal\bhk_summary_paragraphs\SummaryHandlerManager
   */
  protected $summaryManager;

  /**
   * Constructs a new ParagraphViewBuilder.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\bhk_summary_paragraphs\SummaryHandlerManager $summary_manager
   *   Summary handler manager.
   * @param \Drupal\Core\Theme\Registry $theme_registry
   *   The theme registry.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager, SummaryHandlerManager $summary_manager, Registry $theme_registry = NULL) {
    parent::__construct($entity_type, $entity_manager, $language_manager, $theme_registry);

    $this->summaryManager = $summary_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('plugin.manager.bhk_summary_paragraphs_handler'),
      $container->get('theme.registry')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildMultiple(array $build_list) {
    $build_list = parent::buildMultiple($build_list);

    foreach ($build_list as $key => $value) {
      $paragraph_type = $value['#paragraph']->getParagraphType();

      if ($paragraph_type->getThirdPartySetting('bhk_summary_paragraphs', 'enable_summary', FALSE)) {
        $selected_plugin = $paragraph_type->getThirdPartySetting('bhk_summary_paragraphs', 'summary_plugin', FALSE);

        if ($selected_plugin) {
          $summary_handler = $this->summaryManager->createInstance($selected_plugin);
          $summary_handler->buildSummary($build_list[$key], $value['#paragraph'], $paragraph_type);
        }
      }
    }

    return $build_list;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Summary has to be updated every time content is.
    $tags = parent::getCacheTags() ?: [];
    $tags[] = 'node_list';

    return $tags;
  }

}
