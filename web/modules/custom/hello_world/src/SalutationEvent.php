<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 1/2/18
 * Time: 8:28 PM
 */

namespace Drupal\hello_world;

use Symfony\Component\EventDispatcher\Event;

class SalutationEvent extends Event{

  const EVENT = 'hello_world.salutation_event';

  /**
   * The salutation message.
   *
   * @var string
   */
  protected $message;

  /**
   * @return string
   */
  public function getValue() {
    return $this->message;
  }

  /**
   * @param mixed $message
   */
  public function setValue($message) {
    $this->message = $message;
  }


}