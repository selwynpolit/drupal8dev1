<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 12/5/17
 * Time: 12:27 PM
 */

namespace Drupal\dino_roar\Jurassic;


use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DinoListener implements EventSubscriberInterface {

  /**
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  public function __construct(LoggerChannelFactoryInterface $loggerChannelFactory) {

    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  public function onKernelRequest(GetResponseEvent $event) {
    $request = $event->getRequest();
    $shouldRoar = $request->query->get('roar');
    if ($shouldRoar) {
      $this->loggerChannelFactory->get('default')
        ->debug('Roar Requested ROOOOAAAARRR!');

      // Play with the request object methods a bit..
      $method = $request->getMethod();
//      echo '<pre>' . var_export($method, true) . '</pre>';

      //Get headers from the request object...
      $headers = $request->headers->all();
//      echo '<pre>' . var_export($headers['user-agent'], true) . '</pre>';

      drupal_set_message(t("method = $method", ['@method' => $method] ));


    }

  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => 'onKernelRequest',
    ];
  }
}