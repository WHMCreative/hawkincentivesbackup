<?php

namespace Drupal\e3_marketo\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\Renderer;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Base class for Marketo Handler plugins.
 *
 * @ingroup e3_marketo
 */
abstract class MarketoHandlerBase extends PluginBase implements MarketoHandlerInterface {

  use StringTranslationTrait;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Marketo settings.
   *
   * @var array
   */
  protected $marketoConfig;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Config Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Request Stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  public static $loadedMarketoSettings;

  /**
   * Constructs a MarketoHandlerBase object.
   *
   * @param array $configuration
   *   Plugin Configuration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin Definition.
   * @param \Drupal\Core\Database\Driver\mysql\Connection $database
   *   Database Connection.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   Config Factory.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current User.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   Renderer service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Messenger service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request Stack.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Connection $database,
    ConfigFactory $config_factory,
    AccountInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
    Renderer $renderer,
    MessengerInterface $messenger,
    RequestStack $request_stack) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->database = $database;
    $this->configFactory = $config_factory;
    $this->marketoConfig = $config_factory->get('e3_marketo.settings');
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->messenger = $messenger;
    $this->requestStack = $request_stack;
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
      $container->get('config.factory'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('messenger'),
      $container->get('request_stack')
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
  public function getDescription() {
    return isset($this->pluginDefinition['description']) ? $this->pluginDefinition['description'] : '';
  }

  /**
   * {@inheritdoc}
   */
  public function applies(MarketoFormEntityInterface $marketo_form, $callback, &$arguments = NULL) {

    // Make sure selected callback exists within the handler.
    if (method_exists($this, $callback)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
