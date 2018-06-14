<?php

namespace Drupal\e3_marketo;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\e3_marketo\Entity\MarketoFormEntityInterface;

/**
 * Defines the storage handler class for Marketo form entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Marketo form entity entities.
 *
 * @ingroup e3_marketo
 */
class MarketoFormEntityStorage extends SqlContentEntityStorage implements MarketoFormEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(MarketoFormEntityInterface $entity) {
    return $this->database->select('marketo_form_revision', 'mfr')
      ->fields('mfr', ['vid'])
      ->condition('mfr.id', $entity->id())
      ->orderBy('mfr.vid')
      ->execute()
      ->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->select('marketo_form_field_revision', 'mfr')
      ->fields('mfr', ['vid'])
      ->condition('mfr.uid', $account->id())
      ->orderBy('mfr.vid')
      ->execute()
      ->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(MarketoFormEntityInterface $entity) {
    return $this->database->select('marketo_form_field_revision', 'mfr')
      ->condition('mfr.id', $entity->id())
      ->condition('mfr.default_langcode', 1)
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('marketo_form_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
