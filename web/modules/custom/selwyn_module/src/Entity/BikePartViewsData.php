<?php

namespace Drupal\selwyn_module\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Bike part entities.
 */
class BikePartViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
