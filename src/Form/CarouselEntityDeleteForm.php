<?php

namespace Drupal\carousel_block\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for deleting Carousel entity entities.
 *
 * @ingroup carousel_block
 */
class CarouselEntityDeleteForm extends ContentEntityDeleteForm {
  public function submitForm(array &$form, FormStateInterface $form_state) {
    Cache::invalidateTags(['entity_type:carousel']);
    parent::submitForm($form, $form_state);

  }

}
