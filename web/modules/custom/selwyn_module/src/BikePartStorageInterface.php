<?php

namespace Drupal\selwyn_module;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\selwyn_module\Entity\BikePartInterface;

/**
 * Defines the storage handler class for Bike part entities.
 *
 * This extends the base storage class, adding required special handling for
 * Bike part entities.
 *
 * @ingroup selwyn_module
 */
interface BikePartStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Bike part revision IDs for a specific Bike part.
   *
   * @param \Drupal\selwyn_module\Entity\BikePartInterface $entity
   *   The Bike part entity.
   *
   * @return int[]
   *   Bike part revision IDs (in ascending order).
   */
  public function revisionIds(BikePartInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Bike part author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Bike part revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\selwyn_module\Entity\BikePartInterface $entity
   *   The Bike part entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BikePartInterface $entity);

  /**
   * Unsets the language for all Bike part with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
