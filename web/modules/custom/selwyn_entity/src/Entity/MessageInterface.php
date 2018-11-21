<?php

namespace Drupal\selwyn_entity\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

interface MessageInterface extends ContentEntityInterface {
  /**
   * Gets the message value.
   *
   * @return string
   */
  public function getMessage();

}
