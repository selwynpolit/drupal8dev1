<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 12/4/17
 * Time: 3:48 PM
 */

namespace Drupal\dino_roar\Jurassic;


use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;

class RoarGenerator {

  /**
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  private $keyValueFactory;

  private $useCache;

  /**
   * RoarGenerator constructor.
   *
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $keyValueFactory
   * @param $useCache
   */
  public function __construct(KeyValueFactoryInterface $keyValueFactory, $useCache) {

    $this->keyValueFactory = $keyValueFactory;
    $this->useCache = $useCache;
  }

  public function getRoar($length) {

    $key = 'roar_'. $length;
    $store = $this->keyValueFactory->get('dino');
    if ($this->useCache && $store->has($key)) {
      return $store->get($key);
    }

    sleep(2);

    $string = 'R'.str_repeat('O', $length).'AR!';
    if ($this->useCache) {
      $store->set($key, $string);
    }

    return $string;
  }
}