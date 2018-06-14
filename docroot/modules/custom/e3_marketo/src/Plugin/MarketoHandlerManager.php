<?php

namespace Drupal\e3_marketo\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;

/**
 * Class MarketoHandlerManager.
 *
 * Provides a new Marketo Handler plugin type.
 *
 * @ingroup e3_marketo
 */
class MarketoHandlerManager extends DefaultPluginManager {

  /**
   * Constructs an MarketoHandlerManager object.
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
      'Plugin/MarketoHandler',
      $namespaces,
      $module_handler,
      'Drupal\e3_marketo\Plugin\MarketoHandlerInterface',
      'Drupal\e3_marketo\Annotation\MarketoHandler'
    );
    $this->alterInfo('marketo_handler_info');
    $this->setCacheBackend($cache_backend, 'marketo_handlers');
  }

  /**
   * Retrieve an array of applicable Marketo Handler plugins.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo Form entity.
   * @param string $callback
   *   Callback that will be executed within the handler.
   * @param mixed $arguments
   *   Additional arguments that will be passed into the callback.
   *
   * @return array
   *   Array of applicable handlers, keyed by their priority.
   */
  public function getApplicableProcessors(MarketoFormEntityInterface $marketo_form, $callback, $arguments = NULL) {
    $plugin_definitions = $this->getDefinitions();

    $handlers = [];
    if ($plugin_definitions) {

      foreach ($plugin_definitions as $key => $definition) {

        // Check if plugin applies to current marketo bundle.
        if (!empty($definition['bundles']) && !in_array($marketo_form->bundle(), $definition['bundles'])) {
          continue;
        }

        // Make sure handlers are sorted according to their priority before they run.
        $priority = isset($definition['priority']) ? $definition['priority'] : 0;
        $handler = $this->createInstance($definition['id']);
        $handlers[$priority][$key] = $handler;
      }

      // Sort by priority before moving on.
      krsort($handlers);

      foreach ($handlers as $priority => $instances) {

        /**
         * @var int $key
         * @var \Drupal\e3_marketo\Plugin\MarketoHandlerInterface $plugin
         */
        foreach ($instances as $key => $plugin) {
          // Run additional handler-specific checks. This should allow handlers to define extra conditions for when they
          // should be applied.
          if (!$plugin->applies($marketo_form, $callback, $arguments)) {

            unset($handlers[$priority][$key]);

            if (empty($handlers[$priority])) {
              unset($handlers[$priority]);
            }
          }
        }
      }
    }

    return $handlers;
  }

  /**
   * Invoke selected callback on all applicable handlers.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo Form entity.
   * @param string $callback
   *   Handler callback to execute.
   * @param mixed $arguments
   *   (optional) Wildcard parameter. Could be anything from a simple string to
   *   array or entity. This parameter is passed to both the specified callback
   *   and the applies() check of Marketo handlers.
   */
  public function invokeHandlers(MarketoFormEntityInterface $marketo_form, $callback, &$arguments = NULL) {
    $applicable_handlers = $this->getApplicableProcessors($marketo_form, $callback, $arguments);
    if ($applicable_handlers) {

      foreach ($applicable_handlers as $priority => $handlers) {
        foreach ($handlers as $plugin) {
          $plugin->{$callback}($marketo_form, $arguments);
        }
      }
    }
  }

}
