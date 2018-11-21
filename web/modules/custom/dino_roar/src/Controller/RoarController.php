<?php

namespace Drupal\dino_roar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\dino_roar\Jurassic\RoarGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class RoarController extends ControllerBase {

  /**
   * @var \Drupal\dino_roar\Jurassic\RoarGenerator
   */
  private $roarGenerator;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerFactoryService;



  public function __construct(RoarGenerator $roarGenerator, LoggerChannelFactoryInterface $loggerFactoryService) {
    $this->roarGenerator = $roarGenerator;
    $this->loggerFactoryService = $loggerFactoryService;
  }

  public static function create(ContainerInterface $container) {
  $roarGenerator = $container->get('dino_roar.roar_generator');
  $loggerFactory = $container->get('logger.factory');

  return new static($roarGenerator, $loggerFactory);
  }

  public function roar($count) {
    // Explore getting the request object and dump some of it..
    $request = \Drupal::request();
    $method = $request->getMethod();
    echo '<pre>' . var_export($method, true) . '</pre>';
    //Get headers from the request object...
    $headers = $request->headers->all();
    echo '<pre>' . var_export($headers['user-agent'], true) . '</pre>';
    //Get session info and play with it for fun..
    $session = $request->getSession();
    $value = $session->get('mymodule_count', 0);
    $session->set('mymodule_count', $value + 1);

    // load the 'dino' store
    //    $keyValueStore = $this->keyValue('dino');

    $roar = $this->roarGenerator->getRoar($count);

    // Store the $roar var in the key 'roar_string' of the 'dino' store.
    //    $keyValueStore->set('roar_string', $roar);
    // Retrieve the 'roar_string' from the 'dino' store.
    //    $roar = $keyValueStore->get('roar_string');



    // Log the roar to watchdog
    $this->loggerFactoryService->get('default')
      ->debug($roar);

    return [
      '#title' => $roar,
    ];


    return new Response($roar);
  }




}
