<?php

namespace Drupal\carousel_block\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Carousel entity entities.
 *
 * @ingroup carousel_block
 */
interface CarouselEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Carousel entity name.
   *
   * @return string
   *   Name of the Carousel entity.
   */
  public function getName();

  /**
   * Sets the Carousel entity name.
   *
   * @param string $name
   *   The Carousel entity name.
   *
   * @return \Drupal\carousel_block\Entity\CarouselEntityInterface
   *   The called Carousel entity entity.
   */
  public function setName($name);

  /**
   * Gets the Product image.
   *
   * @return \Drupal\file\FileInterface
   */
  public function getImage();

  /**
   * Sets the Product image.
   *
   * @param int $image
   *
   * @return \Drupal\carousel\Entity\CarouselInterface
   *   The called Product entity.
   */
  public function setImage($image);

  /**
   * @return mixed
   */
  public function getCaptionText();

  /**
   * @param $caption_text
   *
   * @return mixed
   */
  public function setCaptionText($caption_text);

  /**
   * Gets the Carousel entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Carousel entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Carousel entity creation timestamp.
   *
   * @param int $timestamp
   *   The Carousel entity creation timestamp.
   *
   * @return \Drupal\carousel_block\Entity\CarouselEntityInterface
   *   The called Carousel entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Carousel entity published status indicator.
   *
   * Unpublished Carousel entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Carousel entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Carousel entity.
   *
   * @param bool $published
   *   TRUE to set this Carousel entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\carousel_block\Entity\CarouselEntityInterface
   *   The called Carousel entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Carousel entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Carousel entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\carousel_block\Entity\CarouselEntityInterface
   *   The called Carousel entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Carousel entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Carousel entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\carousel_block\Entity\CarouselEntityInterface
   *   The called Carousel entity entity.
   */
  public function setRevisionUserId($uid);

}
