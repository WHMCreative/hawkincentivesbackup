<?php

namespace Drupal\bhk_card_browser;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\bhk_card_browser\Entity\CardInterface;

/**
 * Defines the storage handler class for Card entities.
 *
 * This extends the base storage class, adding required special handling for
 * Card entities.
 *
 * @ingroup bhk_card_browser
 */
interface CardStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Card revision IDs for a specific Card.
   *
   * @param \Drupal\bhk_card_browser\Entity\CardInterface $entity
   *   The Card entity.
   *
   * @return int[]
   *   Card revision IDs (in ascending order).
   */
  public function revisionIds(CardInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Card author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Card revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\bhk_card_browser\Entity\CardInterface $entity
   *   The Card entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CardInterface $entity);

  /**
   * Unsets the language for all Card with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
