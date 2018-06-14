<?php

namespace Drupal\e3_marketo\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Configuration form for general marketo settings.
 *
 * @package Drupal\e3_marketo\Form
 *
 * @ingroup e3_marketo
 */
class MarketoFormEntitySettingsForm extends ConfigFormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'MarketoFormEntitySettings';
  }

  /**
   * Constructs a \Drupal\e3_marketo\Form\MarketoFormEntitySettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory, Connection $database) {
    parent::__construct($config_factory);

    $this->setConfigFactory($config_factory);
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('database')
    );
  }

  /**
   * @inheritdoc
   */
  protected function getEditableConfigNames() {
    return [
      'e3_marketo.settings'
    ];
  }

  /**
   * Defines the settings form for Marketo form entity entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('e3_marketo.settings');

    // Main Marketo settings.
    $form['main_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Marketo Settings'),
      'instance_host' => [
        '#type' => 'textfield',
        '#title' => $this->t('Marketo Instance Host'),
        '#default_value' => $config->get('instance_host'),
        '#required' => TRUE,
        '#description' => $this->t('Host for the Marketo instance, e.g. "//app-xxxx.marketo.com"'),
      ],
      'alt_instance_host' => [
        '#type' => 'textfield',
        '#title' => $this->t('Alternative Marketo Instance Host'),
        '#default_value' => $config->get('alt_instance_host'),
        '#description' => $this->t('Alternative Host for the Marketo instance, this is usually the Marketo Landing Pages domain"'),
      ],
      'munchkin_id' => [
        '#type' => 'textfield',
        '#title' => 'Munchkin ID',
        '#required' => TRUE,
        '#default_value' => $config->get('munchkin_id')? : '',
        '#description' => $this->t('Munchkin ID in a format XXX-XXX-XXX.')
      ],
      'client_id' => [
        '#type' => 'textfield',
        '#title' => 'Client ID',
        '#default_value' => $config->get('client_id')? : '',
      ],
      'client_secret' => [
        '#type' => 'textfield',
        '#title' => 'Client Secret',
        '#default_value' => $config->get('client_secret')? : '',
      ],
      'endpoint_path' => [
        '#type' => 'textfield',
        '#title' => 'Rest Endpoint Path',
        '#default_value' => $config->get('endpoint_path')? : '',
        '#description' => $this->t('Base path for REST calls, e.g. "https://XXX-XXX-XXX.mktorest.com/rest".')
      ],
      'identity_endpoint_path' => [
        '#type' => 'textfield',
        '#title' => 'Identity Endpoint Path',
        '#default_value' => $config->get('identity_endpoint_path')? : '',
        '#description' => $this->t('Base path for authentication, e.g. "https://XXX-XXX-XXX.mktorest.com/identity".')
      ],
      'load_error_message' => [
        '#type' => 'text_format',
        '#title' => $this->t('Marketo Blocked Message'),
        '#default_value' => $config->get('load_error_message.value'),
        '#description' => $this->t('Message to display to the user in case Marketo assets failed to be loaded (might happen due to tracking blockers)'),
        '#format' => !empty($config->get('load_error_message.format')) ? $config->get('load_error_message.format') : filter_default_format(),
      ],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Configuration'),
      '#submit' => ['::submitForm'],
      '#button_type' => 'primary',
    ];

    $form['#tree'] = TRUE;
    return $form;
  }

  /**
   * @inheritdoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Verify patterns of Marketo settings.
    if (isset($values['main_settings'])) {

      $settings = $values['main_settings'];

      // Make sure that instance host follows the correct pattern.
      if (!preg_match('/^\/\/app-[a-z0-9]{1,5}\.marketo\.com$/', $settings['instance_host'])) {

        $form_state->setError(
          $form['main_settings']['instance_host'],
          $this->t('Instance host does not match the correct pattern - "//app-xxxx.marketo.com"')
        );
      }

      // Make sure that munchkin id follows the correct pattern.
      if (!preg_match('/^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{3}$/', $settings['munchkin_id'])) {

        $form_state->setError(
          $form['main_settings']['munchkin_id'],
          $this->t('Munchkin ID does not match the correct pattern - "XXX-XXX-XXX"')
        );
      }
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('e3_marketo.settings');

    // Save Main Settings.
    if (isset($values['main_settings'])) {
      foreach ($values['main_settings'] as $key => $value) {
        $config->set($key, $value);
      }
    }

    $config->save();
    $this->messenger()->addStatus($this->t('Marketo Settings have been successfully saved'));
  }

}
