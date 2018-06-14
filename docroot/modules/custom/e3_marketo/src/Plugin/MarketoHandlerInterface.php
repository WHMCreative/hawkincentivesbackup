<?php

namespace Drupal\e3_marketo\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;

/**
 * Interface for Marketo Handler plugins.
 *
 * @ingroup e3_marketo
 */
interface MarketoHandlerInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Get the name of the Marketo Handler plugin.
   *
   * @return string
   *   Plugin Label.
   */
  public function getLabel();

  /**
   * Retrieve plugin description.
   *
   * @return string
   *   Plugin Description.
   */
  public function getDescription();

  /**
   * Embed Marketo Form into the Build array of Marketo Form entity.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo form entity.
   * @param array $build
   *   Build array for Marketo Form entity.
   *
   * @return array
   *   Updated build array.
   */
  public function embedMarketoForm(MarketoFormEntityInterface $marketo_form, array &$build);

  /**
   * Check if handler is allowed to run on the given marketo form entity.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $marketo_form
   *   Marketo Form entity.
   * @param string $callback
   *   Callback function run the check for.
   * @param mixed $arguments
   *   Additional arguments that will be used for the callback.
   *
   * @return bool
   *   TRUE if handler applies to specified entity, FALSE otherwise.
   */
  public function applies(MarketoFormEntityInterface $marketo_form, $callback, &$arguments = NULL);

}
