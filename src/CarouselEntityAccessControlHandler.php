<?php

namespace Drupal\carousel_block;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Carousel entity entity.
 *
 * @see \Drupal\carousel_block\Entity\CarouselEntity.
 */
class CarouselEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\carousel_block\Entity\CarouselEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished carousel entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published carousel entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit carousel entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete carousel entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add carousel entity entities');
  }

}
