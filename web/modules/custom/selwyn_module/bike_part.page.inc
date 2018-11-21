<?php

/**
 * @file
 * Contains bike_part.page.inc.
 *
 * Page callback for Bike part entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Bike part templates.
 *
 * Default template: bike_part.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_bike_part(array &$variables) {
  // Fetch BikePart Entity Object.
  $bike_part = $variables['elements']['#bike_part'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
