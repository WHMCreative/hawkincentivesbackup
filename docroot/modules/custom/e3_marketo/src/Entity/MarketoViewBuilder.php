<?php

namespace Drupal\e3_marketo\Entity;

use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Render\RendererInterface;
use Drupal\e3_marketo\Plugin\MarketoHandlerManager;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Theme\Registry;

/**
 * View Builder for Marketo Forms.
 */
class MarketoViewBuilder extends EntityViewBuilder {

  /**
   * Marketo Handlers Manager.
   *
   * @var \Drupal\e3_marketo\Plugin\MarketoHandlerManager
   */
  protected $marketoHandlerManager;

  /**
   * Renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a new MarketoViewBuilder.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\e3_marketo\Plugin\MarketoHandlerManager $marketo_handler_manager
   *   Marketo Handler Manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer service.
   * @param \Drupal\Core\Theme\Registry $theme_registry
   *   The theme registry.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager, MarketoHandlerManager $marketo_handler_manager, RendererInterface $renderer, Registry $theme_registry = NULL) {
    parent::__construct($entity_type, $entity_manager, $language_manager, $theme_registry);

    $this->marketoHandlerManager = $marketo_handler_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('plugin.manager.marketo_handler_manager'),
      $container->get('renderer'),
      $container->get('theme.registry')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    parent::buildComponents($build, $entities, $displays, $view_mode);

    // Embed Marketo forms using applicable plugins.
    foreach ($entities as $id => $entity) {

      // If the form is referenced, add corresponding cacheable dependency.
      if (!empty($entity->_referringItem)) {
        /** @var ParagraphInterface $reference_paragraph */
        $reference_paragraph = $entity->_referringItem->getEntity();

        if ($reference_paragraph instanceof ParagraphInterface) {
          $this->renderer->addCacheableDependency($build[$id], $reference_paragraph);
        }
      }

      if ($displays[$entity->bundle()]->getComponent('marketo_form_embed')) {
        $this->marketoHandlerManager->invokeHandlers($entity, 'embedMarketoForm', $build[$id]);
      }
    }
  }

}
