<?php

namespace Drupal\selwyn_module;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Bike part entity.
 *
 * @see \Drupal\selwyn_module\Entity\BikePart.
 */
class BikePartAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\selwyn_module\Entity\BikePartInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished bike part entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published bike part entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit bike part entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete bike part entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add bike part entities');
  }

}
