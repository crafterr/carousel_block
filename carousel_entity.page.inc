<?php

/**
 * @file
 * Contains carousel_entity.page.inc.
 *
 * Page callback for Carousel entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Carousel entity templates.
 *
 * Default template: carousel_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_carousel_entity(array &$variables) {
  // Fetch CarouselEntity Entity Object.
  $carousel_entity = $variables['elements']['#carousel_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
