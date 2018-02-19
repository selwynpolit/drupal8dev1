<?php

namespace Drupal\iai_wea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use \Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use \Drupal\iai_personalization\PersonalizationIpServiceInterface;


/**
 * Class EcoActionBlock
 *
 * @Block(
 *   id = "eco_action_block",
 *   admin_label = @Translation("Eco Action Block"),
 *   category = @Translation("Custom")
 * )
 */
class EcoActionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage for acquifers.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Entity repository (gets the correct translation).
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;


  /**
   * @var \Drupal\iai_personalization\PersonalizationIpServiceInterface
   */
  protected $personalizationIpService;

  /******************************************************************************
   **                                                                          **
   ** This is an example of Dependency Injection. The necessary objects are    **
   ** being injected through the class's constructor.                          **
   **                                                                          **
   ******************************************************************************/

  /**
   * Constructs an EcoActionBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\iai_personalization\PersonalizationIpServiceInterface $personalization_ip_service
   *   The personalization Ip Service.
   *
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository, PersonalizationIpServiceInterface $personalization_ip_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->entityRepository = $entity_repository;
    $this->personalizationIpService = $personalization_ip_service;

  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('iai_personalization.personalization_ip_service')
    );
  }

  /**
   * @inheritDoc
   */
  public function defaultConfiguration() {
    // By default, the block will contain 5 items.
    return array(
      'block_count' => 5,
    );
  }

  /**
   * @inheritDoc
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $range = range(2,20);
    $form['block_count'] = array(
      '#type' => 'select',
      '#default_value' => $this->configuration['block_count'],
      '#options' => array_combine($range, $range),
    );
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['block_count'] = $form_state->getValue('block_count');
  }

  /**
   * build the contents of the block and return them.
   *
   * @inheritdoc
   */
  public function build(){
    $result = $this->nodeStorage->getQuery()
      ->condition('type', 'water_eco_action')
      ->condition('status', '1')
      ->range(0, $this->configuration['block_count'])
      ->sort('title', 'ASC')
      ->execute();

    if ($result) {
      //Only display block if there are items to show.
      $items = $this->nodeStorage->loadMultiple($result);

      $build['list'] = [
        '#theme' => 'item_list',
        '#list_type' => 'ol',
        '#items' => [],
      ];


      // List of titles only.
//      foreach ($items as $item) {
//        $translatedItem = $this->entityRepository->getTranslationFromContext($item);
//        $build['list']['#items'][$item->id()] = [
//          '#type' => 'markup',
//          '#markup' => $translatedItem->label(),
//        ];
//      }

//      // List of titles linked to their nodes.
//      foreach ($items as $item) {
//        $translatedItem = $this->entityRepository->getTranslationFromContext($item);
//        $options = ['relative' => TRUE];
//        $nid = $item->id();
//        $url = Url::fromRoute('entity.node.canonical',['node' => $nid], $options);


      // Another variant?
      foreach ($items as $item) {
        $translatedItem = $this->entityRepository->getTranslationFromContext($item);
        $nid = $item->id();
        $url = Url::fromUri("internal:/node/$nid");

        $build['list']['#items'][$item->id()] = [
          '#title' => $translatedItem->label(),
          '#type' => 'link',
          '#url' => $url,
        ];
      }



      /******************************************************************************
       ** We don't really need to worry about expiring the cache because we've got **
       ** only three aquifers. Nonethless, we are going to set the meta data so    **
       ** that when pieces of content are updated we make sure to rebuild this     **
       ** block.                                                                   **
       ******************************************************************************/
      $build['#cache']['tags'][] = 'node_list';
      return $build;
    }
  }


}