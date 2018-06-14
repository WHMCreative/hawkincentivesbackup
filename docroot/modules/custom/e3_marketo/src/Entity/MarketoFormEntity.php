<?php

namespace Drupal\e3_marketo\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\UserInterface;

/**
 * Defines the Marketo form entity entity.
 *
 * @ingroup e3_marketo
 *
 * @ContentEntityType(
 *   id = "marketo_form",
 *   label = @Translation("Marketo form entity"),
 *   bundle_label = @Translation("Marketo Form bundle"),
 *   handlers = {
 *     "storage" = "Drupal\e3_marketo\MarketoFormEntityStorage",
 *     "view_builder" = "Drupal\e3_marketo\Entity\MarketoViewBuilder",
 *     "list_builder" = "Drupal\e3_marketo\Entity\Controller\MarketoFormEntityListBuilder",
 *     "views_data" = "Drupal\e3_marketo\Entity\MarketoFormEntityViewsData",
 *     "translation" = "Drupal\e3_marketo\MarketoFormEntityTranslationHandler",
 *     "form" = {
 *       "default" = "Drupal\e3_marketo\Entity\Form\MarketoFormEntityForm",
 *       "add" = "Drupal\e3_marketo\Entity\Form\MarketoFormEntityForm",
 *       "edit" = "Drupal\e3_marketo\Entity\Form\MarketoFormEntityForm",
 *       "delete" = "Drupal\e3_marketo\Entity\Form\MarketoFormEntityDeleteForm",
 *     },
 *     "access" = "Drupal\e3_marketo\MarketoFormEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\e3_marketo\MarketoFormEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "marketo_form",
 *   data_table = "marketo_form_field_data",
 *   revision_table = "marketo_form_revision",
 *   revision_data_table = "marketo_form_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer marketo form entity entities",
 *   bundle_entity_type = "marketo_form_bundle",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "bundle" = "bundle",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "marketo_form_id" = "marketo_form_id"
 *   },
 *   links = {
 *     "canonical" = "/marketo_form/{marketo_form}",
 *     "add-form" = "/marketo_form/add/{marketo_form_bundle}",
 *     "add-page" = "/marketo_form/add",
 *     "edit-form" = "/marketo_form/{marketo_form}/edit",
 *     "delete-form" = "/marketo_form/{marketo_form}/delete",
 *     "version-history" = "/marketo_form/{marketo_form}/revisions",
 *     "revision" = "/marketo_form/{marketo_form}/revisions/{marketo_form_revision}/view",
 *     "revision_revert" = "/marketo_form/{marketo_form}/revisions/{marketo_form_revision}/revert",
 *     "translation_revert" = "/marketo_form/{marketo_form}/revisions/{marketo_form_revision}/revert/{langcode}",
 *     "revision_delete" = "/marketo_form/{marketo_form}/revisions/{marketo_form_revision}/delete",
 *   },
 *   field_ui_base_route = "entity.marketo_form_bundle.edit_form",
 * )
 */
class MarketoFormEntity extends RevisionableContentEntityBase implements MarketoFormEntityInterface {

  use EntityChangedTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the marketo_form owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return $this->get('marketo_form_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFormId($id) {
    if(!is_array($id)){
      $id = (array)$id;
    }

    $this->set('marketo_form_id', $id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isNew() {
    return !empty($this->enforceIsNew) || $this->id() === NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Marketo form entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'region' => 'hidden'
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Marketo form entity entity.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Marketo form entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['marketo_form_id'] = BaseFieldDefinition::create('integer')
      ->setCardinality(1)
      ->setLabel(t('Marketo Form ID'))
      ->setDescription(t('Marketo Form ID'))
      ->setReadOnly(FALSE)
      ->setRequired(TRUE)
      ->setSetting('unsigned', TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'region' => 'hidden'
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $url_route_parameters = parent::urlRouteParameters($rel);

    if (in_array($rel, ['revision', 'revision_revert', 'translation_revert', 'revision_delete'])) {
      $url_route_parameters['marketo_form_revision'] = $this->getLoadedRevisionId();
    }

    return $url_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'url.path';
    $contexts[] = 'url.query_args';
    return $contexts;
  }

}
