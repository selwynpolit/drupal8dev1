<?php

namespace Drupal\selwyn_module;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class BikePartStorage extends SqlContentEntityStorage implements BikePartStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BikePartInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {bike_part_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {bike_part_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BikePartInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {bike_part_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('bike_part_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
