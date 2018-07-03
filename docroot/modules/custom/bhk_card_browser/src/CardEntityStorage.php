<?php

namespace Drupal\bhk_card_browser;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Defines the storage handler class for Card entities.
 *
 * This extends the base storage class, adding required special handling for
 * Card entities.
 *
 * @ingroup bhk_card_browser
 */
class CardEntityStorage extends SqlContentEntityStorage implements CardEntityStorageInterface {

}
