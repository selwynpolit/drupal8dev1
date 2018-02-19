<?php

namespace Drupal\hello_world;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;


/**
 * Prepares the salutation to the world.
 */
class HelloWorldSalutation implements HelloWorldSalutationInterface  {

  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * HelloWorldSalutation constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }


  /**
   * Returns the salutation
   */
  public function getSalutation() {
    $config = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');
    if ($salutation != "") {
      return $salutation;
    }

    $time = new \DateTime();
    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      return $this->t('Good morning you fabulous world');
    }
    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      return $this->t('Good afternoon you fabulous world');
    }
    if ((int) $time->format('G') >= 18) {
      return $this->t('Good evening you fabulous world');
    }
  }

  /**
   * Returns a the Salutation render array.
   */
  public function getSalutationComponent() {
//    $this->killSwitch->trigger();
    $render = [
      '#theme' => 'hello_world_salutation',
      '#salutation' => [
        '#contextual_links' => [
          'hello_world' => [
            'route_parameters' => []
          ],
        ]
      ],
      '#cache' => [
        'max-age' => 0
      ]
    ];

    $config = $this->configFactory->get('hello_world.custom_salutation');
    $render['#cache']['tags'] = $config->getCacheTags();
    $salutation = $config->get('salutation');

    if ($salutation != "") {
      $render['#salutation']['#markup'] = $salutation;
      $render['#overridden'] = TRUE;
      return $render;
    }

    $time = new \DateTime();
    $render['#target'] = $this->t('world');
    $render['#attached'] = [
      'library' => [
        'hello_world/hello_world_clock'
      ]
    ];

    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      $render['#salutation']['#markup'] = $this->t('Good morning');
      return $render;
    }

    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      $render['#salutation']['#markup'] = $this->t('Good afternoon');
      // Attach something to the render array.
      $render['#attached']['drupalSettings']['hello_world']['hello_world_clock']['afternoon'] = TRUE;
      return $render;
    }

    if ((int) $time->format('G') >= 18) {
      $render['#salutation']['#markup'] = $this->t('Good evening');
      return $render;
    }
  }


  public function makeLink() {

    //Using link generator to create a GeneratedLink.
    $url = Url::fromUri('internal:/node/1');
    $link = \Drupal::service('link_generator')->generate('My link', $url);


    // Generate a link to http://dev1/reports/search?user=admin.
    $option = [
      'query' => ['user' => 'admin'],
    ];
    $url = Url::fromUri('internal:/admin/people', $option);


    // or use the Link class.
//    $url = Url::fromRoute('my other route');
    $link = Link::fromTextAndUrl('My link', $url);
    $renderable_array = $link->toRenderable();
    return $renderable_array;

//    $link = Link::fromTextAndUrl('My link', $url);
//    $link = Link::fromTextAndUrl(t('My cool link'), $url);
//
//
//    $link = $link->getGeneratedLink();
//    $renderable_array = ;
//    return $link;

//    \Drupal::l is deprecated. Maybe this case will be useful for somebody

//  use Drupal\Core\Url;
//  use Drupal\Core\Link;
//  $url = Url::fromRoute('entity.node.edit_form', array('node' => NID));
//  $project_link = Link::fromTextAndUrl(t('Open Project'), $url);
//  $project_link = $project_link->toRenderable();
//  // If you need some attributes.
//  $project_link['#attributes'] = array('class' => array('button', 'button-action', 'button--primary', 'button--small'));
//  print render($project_link);




  }

}