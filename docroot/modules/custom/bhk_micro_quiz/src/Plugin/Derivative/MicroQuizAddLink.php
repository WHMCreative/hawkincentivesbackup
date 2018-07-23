<?php

namespace Drupal\bhk_micro_quiz\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the add links for MicroQuiz entities.
 *
 * @package Drupal\bhk_micro_quiz\Plugin\Derivative
 */
class MicroQuizAddLink extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a MicroQuizAddLink instance.
   *
   * @param string $base_plugin_id
   *   Base plugin ID.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface  $entity_type_manager
   *   Entity type manager.
   */
  public function __construct($base_plugin_id, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    // Grab all MicroQuiz bundles and generate a derivative link per bundle.
    $micro_quiz_bundles = $this->entityTypeManager->getStorage('micro_quiz_type')->loadMultiple();
    foreach ($micro_quiz_bundles as $id => $mq_bundle) {
      $links[$id] = [
          'title' => $this->t("Add @label", ['@label' => $mq_bundle->label()]),
          'route_name' => 'entity.micro_quiz.add_form',
          'route_parameters' => ['micro_quiz_type' => $mq_bundle->id()],
        ] + $base_plugin_definition;
    }

    return $links;
  }
}