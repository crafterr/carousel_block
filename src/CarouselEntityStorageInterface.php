<?php

namespace Drupal\carousel_block;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\carousel_block\Entity\CarouselEntityInterface;

/**
 * Defines the storage handler class for Carousel entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Carousel entity entities.
 *
 * @ingroup carousel_block
 */
interface CarouselEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Carousel entity revision IDs for a specific Carousel entity.
   *
   * @param \Drupal\carousel_block\Entity\CarouselEntityInterface $entity
   *   The Carousel entity entity.
   *
   * @return int[]
   *   Carousel entity revision IDs (in ascending order).
   */
  public function revisionIds(CarouselEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Carousel entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Carousel entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\carousel_block\Entity\CarouselEntityInterface $entity
   *   The Carousel entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CarouselEntityInterface $entity);

  /**
   * Unsets the language for all Carousel entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
