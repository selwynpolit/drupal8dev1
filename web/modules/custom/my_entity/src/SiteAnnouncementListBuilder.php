<?php

namespace Drupal\my_entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\my_entity\Entity\SiteAnnouncementInterface;

class SiteAnnouncementListBuilder extends ConfigEntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Label');
    return $header + parent::buildHeader();
  }
  /**
   * {@inheritdoc}
   */
  public function buildRow(SiteAnnouncementInterface $entity) {
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }
}

  