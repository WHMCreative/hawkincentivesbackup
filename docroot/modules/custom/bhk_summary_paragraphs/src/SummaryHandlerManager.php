<?php

namespace Drupal\bhk_summary_paragraphs;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Component\Utility\Html;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;

/**
 * Plugin manager for Summary Handlers.
 */
class SummaryHandlerManager extends DefaultPluginManager {

  use StringTranslationTrait;

  /**
   * Constructs an SummaryHandlerManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/SummaryHandler',
      $namespaces,
      $module_handler,
      'Drupal\bhk_summary_paragraphs\SummaryHandlerInterface',
      'Drupal\bhk_summary_paragraphs\Annotation\SummaryHandler'
    );
    $this->alterInfo('fw_summary_builder_info');
    $this->setCacheBackend($cache_backend, 'bhk_summary_paragraphs');
  }

  /**
   * Retrieves an options list of available summary plugins.
   *
   * @return string[]
   *   An associative array mapping the IDs of all available summary plugins to
   *   their labels.
   */
  public function getOptionsList() {
    $options = [];
    foreach ($this->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = Html::escape($plugin_definition['label']);
    }
    return $options;
  }

  /**
   * Add default set of fields.
   *
   * Add a default set of fields to Summary Handler enabled entities:
   *   - Summary Title (field_summary_title),
   *   - Total Count (field_summary_count),
   *   - Select Items Directly (field_summary_direct),
   *   - Summary Content (field_summary_content).
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Paragraphs entity to add fields to.
   */
  public function addDefaultFields(EntityInterface $entity) {
    $entity_type_id = 'paragraph';
    $default_fields = $this->getDefaultFieldsData($entity);

    /*
     * Loop through default fields, check if they already exist and have been
     * added to the entity. Otherwise create corresponding storage, field, and
     * add to entity.
     */
    foreach ($default_fields as $field_name => $field_data) {

      // Check storage first.
      $field_storage = FieldStorageConfig::loadByName($entity_type_id, $field_name);
      if (empty($field_storage)) {
        $field_storage = FieldStorageConfig::create([
          'field_name' => $field_name,
          'entity_type' => $entity_type_id,
          'type' => $field_data['type'],
          'settings' => isset($field_data['storage_settings']) ? $field_data['storage_settings'] : [],
          'cardinality' => isset($field_data['cardinality']) ? $field_data['cardinality'] : 1,
        ]);
        $field_storage->save();
      }

      // Check for the field.
      $field = FieldConfig::loadByName($entity_type_id, $entity->id(), $field_name);
      if (empty($field)) {
        $field = FieldConfig::create([
          'field_name' => $field_name,
          'entity_type' => $entity_type_id,
          'field_storage' => $field_storage,
          'bundle' => $entity->id(),
          'label' => $field_data['label'],
          'description' => isset($field_data['description']) ? $field_data['description'] : '',
          'default_value' => isset($field_data['default_value']) ? $field_data['default_value'] : '',
          'settings' => isset($field_data['settings']) ? $field_data['settings'] : [],
        ]);
        $field->save();
      }

      // Configure default form display for the field.
      $form_display = EntityFormDisplay::load("$entity_type_id.{$entity->id()}.default");

      // If display does not exist yet, create it.
      if (!$form_display) {
        $form_display = EntityFormDisplay::create([
          'targetEntityType' => $entity_type_id,
          'bundle' => $entity->id(),
          'mode' => 'default',
          'status' => TRUE,
        ]);

        $form_display->save();
      }

      if ($form_display && empty($form_display->getComponent($field_name))) {

        // Assign widget settings for the 'default' form mode.
        $form_display->setComponent($field_name, [
          'type' => $field_data['form_widget'],
          'weight' => isset($field_data['weight']) ? $field_data['weight'] : 0,
        ]);
        $form_display->save();
      }

      // Configure default view display for the field.
      $view_display = EntityViewDisplay::load("$entity_type_id.{$entity->id()}.default");

      // If display does not exist yet, create it.
      if (!$view_display) {
        $view_display = EntityViewDisplay::create([
          'targetEntityType' => $entity_type_id,
          'bundle' => $entity->id(),
          'mode' => 'default',
          'status' => TRUE,
        ]);
        $view_display->save();
      }

      if ($view_display && empty($view_display->getComponent($field_name))) {

        // Assign widget settings for the 'default' view mode.
        if (isset($field_data['hidden'])) {
          $view_display->removeComponent($field_name);
        }
        else {
          $view_display->setComponent($field_name, [
            'label' => 'hidden',
            'type' => $field_data['view_widget'],
            'weight' => isset($field_data['weight']) ? $field_data['weight'] : 0,
          ]);
        }
        $view_display->save();
      }
    }
  }

  /**
   * Retrieve Default field data for a set of Summary fields.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Paragraphs type entity to extract settings from.
   *
   * @return array
   *   Field, data, keyed by machine name of the field.
   */
  private function getDefaultFieldsData(EntityInterface $entity) {
    return [
      'field_summary_title' => [
        'label' => $this->t('Summary Title'),
        'type' => 'string',
        'form_widget' => 'string_textfield',
        'view_widget' => 'string',
        'weight' => 0,
      ],
      'field_summary_count' => [
        'label' => $this->t('Total Count'),
        'description' => $this->t('Total number of items to display in this summary.'),
        'type' => 'integer',
        'form_widget' => 'number',
        'hidden' => TRUE,
        'default_value' => 3,
        'weight' => 1,
        'settings' => [
          'min' => 1,
          'max' => 15,
        ],
      ],
      'field_summary_direct' => [
        'label' => $this->t('Select Items Directly'),
        'description' => $this->t('Select exact items to show in this summary.'),
        'type' => 'boolean',
        'form_widget' => 'boolean_checkbox',
        'hidden' => TRUE,
        'default_value' => 0,
        'weight' => 2,
      ],
      'field_summary_content' => [
        'label' => $this->t('Summary Content'),
        'description' => $this->t('Optional. Select specific items to show in this summary.'),
        'type' => 'entity_reference',
        'form_widget' => 'entity_reference_autocomplete',
        'storage_settings' => [
          'target_type' => 'node',
        ],
        'settings' => [
          'handler' => 'default:node',
          'handler_settings' => [
            'target_bundles' => $entity->getThirdPartySetting('bhk_summary_paragraphs', 'summary_content_types', []),
          ],
        ],
        'cardinality' => -1,
        'hidden' => TRUE,
        'weight' => 3,
      ],
    ];
  }

}
