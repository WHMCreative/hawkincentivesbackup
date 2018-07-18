<?php

namespace Drupal\bhk_micro_quiz\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;

/**
 * Defines the MicroQuiz type entity.
 *
 * @ConfigEntityType(
 *   id = "micro_quiz_type",
 *   label = @Translation("MicroQuiz Type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bhk_micro_quiz\MicroQuizTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bhk_micro_quiz\Entity\Form\MicroQuizTypeForm",
 *       "edit" = "Drupal\bhk_micro_quiz\Entity\Form\MicroQuizTypeForm",
 *       "delete" = "Drupal\bhk_micro_quiz\Entity\Form\MicroQuizTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\bhk_micro_quiz\MicroQuizTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "micro_quiz_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "micro_quiz",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/micro_quiz_bundles/{micro_quiz_type}",
 *     "add-form" = "/admin/structure/micro_quiz_bundles/add",
 *     "edit-form" = "/admin/structure/micro_quiz_bundles/{micro_quiz_type}/edit",
 *     "delete-form" = "/admin/structure/micro_quiz_bundles/{micro_quiz_type}/delete",
 *     "collection" = "/admin/structure/micro_quiz_bundles"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class MicroQuizType extends ConfigEntityBundleBase implements MicroQuizTypeInterface {

  /**
   * The MicroQuiz type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The MicroQuiz bundle label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the MicroQuiz bundle.
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
