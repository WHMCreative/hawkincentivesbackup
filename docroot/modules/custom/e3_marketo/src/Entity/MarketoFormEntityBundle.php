<?php

namespace Drupal\e3_marketo\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\Annotation\ConfigEntityType;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Defines the Marketo Form Bundle configuration entity.
 *
 * @ingroup e3_marketo
 *
 * @ConfigEntityType(
 *   id = "marketo_form_bundle",
 *   label = @Translation("Marketo Form bundle"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\e3_marketo\Entity\Form\MarketoBundleForm",
 *       "edit" = "Drupal\e3_marketo\Entity\Form\MarketoBundleForm",
 *       "delete" = "Drupal\e3_marketo\Entity\Form\MarketoBundleDeleteConfirm"
 *     },
 *     "list_builder" = "Drupal\e3_marketo\Entity\Controller\MarketoBundleListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer marketo form bundles",
 *   config_prefix = "bundle",
 *   bundle_of = "marketo_form",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/marketo_bundles/add",
 *     "edit-form" = "/admin/structure/marketo_bundles/manage/{marketo_form_bundle}",
 *     "delete-form" = "/admin/structure/marketo_bundles/manage/{marketo_form_bundle}/delete",
 *     "collection" = "/admin/structure/marketo_bundles",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "remove_source_styles",
 *   }
 * )
 */
class MarketoFormEntityBundle extends ConfigEntityBundleBase implements ConfigEntityInterface, EntityDescriptionInterface {

  /**
   * The machine name of the marketo form bundle.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the marketo form bundle.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the marketo form bundle.
   *
   * @var string
   */
  protected $description;

  /**
   * Remove Marketo-sourced stylesheets.
   *
   * @var bool
   */
  public $remove_source_styles;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * Check if Marketo-sourced styles have to be removed.
   *
   * @return bool
   *   TRUE if Marketo-sourced styles need to be removed.
   */
  public function getRemoveSourceStyles() {
    return $this->remove_source_styles;
  }

}