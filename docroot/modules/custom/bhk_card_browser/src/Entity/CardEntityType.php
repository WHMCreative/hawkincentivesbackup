<?php

namespace Drupal\bhk_card_browser\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;

/**
 * Defines the Card type entity.
 *
 * @ConfigEntityType(
 *   id = "card_type",
 *   label = @Translation("Card Type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bhk_card_browser\CardEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bhk_card_browser\Entity\Form\CardEntityTypeForm",
 *       "edit" = "Drupal\bhk_card_browser\Entity\Form\CardEntityTypeForm",
 *       "delete" = "Drupal\bhk_card_browser\Entity\Form\CardEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\bhk_card_browser\CardEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "card_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "card",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/card_types/{card_type}",
 *     "add-form" = "/admin/structure/card_types/add",
 *     "edit-form" = "/admin/structure/card_types/{card_type}/edit",
 *     "delete-form" = "/admin/structure/card_types/{card_type}/delete",
 *     "collection" = "/admin/structure/card_types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class CardEntityType extends ConfigEntityBundleBase implements CardEntityTypeInterface {

  /**
   * The Card type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Card type label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the Card type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

}
