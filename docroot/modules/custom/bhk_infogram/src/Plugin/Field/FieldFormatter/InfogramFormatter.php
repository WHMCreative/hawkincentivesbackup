<?php

namespace Drupal\bhk_infogram\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\filter\Render\FilteredMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Infogram' formatter.
 *
 * @FieldFormatter(
 *   id = "infogram",
 *   label = @Translation("Infogram"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class InfogramFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * @var RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      // Add any services you want to inject here
      $container->get('current_route_match')
    );
  }

  /**
   * Construct a MyFormatter object
   *
   * @param $plugin_id
   * @param $plugin_definition
   * @param FieldDefinitionInterface $field_definition
   * @param array $settings
   * @param $label
   * @param $view_mode
   * @param array $third_party_settings
   * @param RouteMatchInterface $route_match
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, RouteMatchInterface $route_match) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if ($this->getSetting('thumbnail_display')) {
      $summary[] = $this->t('Displays as thumbnail');
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $response = json_decode(file_get_contents('https://infogram.com/oembed_iframe?url=' . $item->value));

      $thumbnail = '';
      $html = '';

      if ($this->getSetting('thumbnail_display') || $this->routeMatch->getRouteName() === 'embed.preview') {
        $thumbnail = [
          '#theme' => 'image',
          '#uri' => $response->thumbnail_url,
          '#width' => 180,
        ];
      } else {
        $html = $response->html;
      }

      $element[$delta] = [
        '#theme' => 'infogram_output',
        '#infogram_html' => FilteredMarkup::create($html),
        '#infogram_thumbnail' => $thumbnail,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'thumbnail_display' => FALSE,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['thumbnail_display'] = [
      '#title' => $this->t('Display as thumbnail'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('thumbnail_display'),
    ];

    return $element;
  }

}
