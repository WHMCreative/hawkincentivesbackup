<?php

namespace Drupal\bhk_summary_paragraphs\Form;

use Drupal\paragraphs\Form\ParagraphsTypeForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\ParagraphsBehaviorManager;
use Drupal\Core\Messenger\Messenger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bhk_summary_paragraphs\SummaryHandlerManager;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

/**
 * Class SummaryEnabledParagraphsTypeForm.
 *
 * Form controller override for paragraph type forms to allow any bundle
 * to leverage summary functionality.
 */
class SummaryEnabledParagraphsTypeForm extends ParagraphsTypeForm {

  /**
   * Summary handler manager.
   *
   * @var \Drupal\bhk_summary_paragraphs\SummaryHandlerManager
   */
  protected $summaryManager;

  /**
   * Entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * Entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * SummaryEnabledParagraphsTypeForm constructor.
   *
   * @param \Drupal\paragraphs\ParagraphsBehaviorManager $paragraphs_behavior_manager
   *   The paragraphs type feature manager service.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   Messenger service.
   * @param \Drupal\bhk_summary_paragraphs\SummaryHandlerManager $summary_handler_manager
   *   Summary handler manager.
   * @param \Drupal\Core\Entity\EntityFieldManager $entity_field_manager
   *   Entity field manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   Entity display repository service.
   */
  public function __construct(ParagraphsBehaviorManager $paragraphs_behavior_manager, Messenger $messenger, SummaryHandlerManager $summary_handler_manager, EntityFieldManager $entity_field_manager, EntityDisplayRepositoryInterface $entity_display_repository) {
    parent::__construct($paragraphs_behavior_manager, $messenger);

    $this->summaryManager = $summary_handler_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityDisplayRepository = $entity_display_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.paragraphs.behavior'),
      $container->get('messenger'),
      $container->get('plugin.manager.bhk_summary_paragraphs_handler'),
      $container->get('entity_field.manager'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Add ability to enable summary plugin system for the bundle.
    $form['enable_summary'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable this bundle to leverage Summary Handler functionality.'),
      '#default_value' => $this->entity->getThirdPartySetting('bhk_summary_paragraphs', 'enable_summary', FALSE),
      '#description' => $this->t('If enabled, rendering of paragraphs of this type would be handled by a selected summary plugin.'),
    ];

    $form['summary_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Summary Settings'),
      '#open' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="enable_summary"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Allow selection of desired handler to use.
    $form['summary_settings']['summary_plugin'] = [
      '#type' => 'select',
      '#title' => $this->t('Select summary handler to use'),
      '#options' => $this->summaryManager->getOptionsList(),
      '#description' => $this->t('Choose one of available plugins to handle summary logic. If unsure, select a default one.'),
      '#default_value' => $this->entity->getThirdPartySetting('bhk_summary_paragraphs', 'summary_plugin', 'bhk_summary_paragraphs_handler'),
    ];

    // Allow selection of content types to use.
    $content_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($content_types as $node_type) {
      $content_type_options[$node_type->id()] = $node_type->label();
    }

    $form['summary_settings']['summary_content_types'] = [
      '#type' => 'select',
      '#title' => $this->t('Select content types'),
      '#options' => $content_type_options,
      '#multiple' => TRUE,
      '#description' => $this->t('Choose content types to be displayed in this summary.'),
      '#default_value' => $this->entity->getThirdPartySetting('bhk_summary_paragraphs', 'summary_content_types', FALSE),
    ];

    // Allow to select desired view mode for summary content to display.
    $form['summary_settings']['summary_view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Select view mode'),
      '#options' => $this->entityDisplayRepository->getViewModeOptions('node'),
      '#description' => $this->t('Choose content types to be displayed in this summary.'),
      '#default_value' => $this->entity->getThirdPartySetting('bhk_summary_paragraphs', 'summary_view_mode', 'teaser'),
    ];

    // Allow to optionally add a default set of fields for summary to use.
    $form['summary_settings']['summary_add_fields'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add default fields'),
      '#description' => $this->t("Check this box to add a set of default fields that Summary Handler would utilize for it's functionality."),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Allow selected plugin to validate summary settings.
    if ($form_state->getValue('enable_summary')) {
      $plugin = $this->summaryManager->createInstance($form_state->getValue('summary_plugin'));
      $plugin->validateSummaryForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $fields = [
      'enable_summary',
      'summary_plugin',
      'summary_content_types',
      'summary_view_mode',
      'summary_add_fields',
    ];
    $enable_summary = $form_state->getValue('enable_summary');

    // Save plugin settings, if summary was enabled. Otherwise make sure to
    // clean up any previously set settings.
    if ($enable_summary) {
      foreach ($fields as $field) {
        $this->entity->setThirdPartySetting('bhk_summary_paragraphs', $field, $form_state->getValue($field));
      }
    }
    else {
      foreach ($fields as $field) {
        $this->entity->unsetThirdPartySetting('bhk_summary_paragraphs', $field);
      }
    }

    parent::save($form, $form_state);

    // Add default set of fields to paragraph if this option was selected.
    if ($enable_summary && $form_state->getValue('summary_add_fields')) {
      $this->summaryManager->addDefaultFields($this->entity);
    }

    // Clear cache definitions for fields.
    $this->entityFieldManager->clearCachedFieldDefinitions();
  }

}
