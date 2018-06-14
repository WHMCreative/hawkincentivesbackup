<?php

namespace Drupal\e3_marketo;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface MarketoFormEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Marketo form entity revision IDs for a specific Marketo form entity.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $entity
   *   The Marketo form entity entity.
   *
   * @return int[]
   *   Marketo form entity revision IDs (in ascending order).
   */
  public function revisionIds(MarketoFormEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Marketo form entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Marketo form entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\e3_marketo\Entity\MarketoFormEntityInterface $entity
   *   The Marketo form entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(MarketoFormEntityInterface $entity);

  /**
   * Unsets the language for all Marketo form entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
