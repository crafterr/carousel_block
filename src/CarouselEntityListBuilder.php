<?php

namespace Drupal\carousel_block;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Carousel entity entities.
 *
 * @ingroup carousel_block
 */
class CarouselEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Carousel entity ID');
    $header['name'] = $this->t('Name');
    $header['image'] = $this->t('Image');
    $header['caption_text'] = $this->t('Caption Text');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\carousel_block\Entity\CarouselEntity */
    $row['id'] = $entity->id();

    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.carousel_entity.edit_form',
      ['carousel_entity' => $entity->id()]
    );

    $row['image']['data'] =  $entity->getImage()->view([
      'type' => 'image',
      'label' => 'hidden',
      'settings' => array(
        'image_style' => 'thumbnail',
      ),
    ]);
    $row['caption_text'] = $entity->getCaptionText();
    return $row + parent::buildRow($entity);
  }

}
