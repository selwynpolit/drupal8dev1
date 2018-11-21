<?php

namespace Drupal\crap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Datetime\DateFormatter;

/**
 * Class WorkshopForm.
 */
class WorkshopForm extends ConfigFormBase {

  /**
   * Drupal\Component\Datetime\Time definition.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $datetimeTime;
  /**
   * Drupal\Core\Datetime\DateFormatter definition.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;
  /**
   * Constructs a new WorkshopForm object.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
      Time $datetime_time,
    DateFormatter $date_formatter
    ) {
    parent::__construct($config_factory);
        $this->datetimeTime = $datetime_time;
    $this->dateFormatter = $date_formatter;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
            $container->get('datetime.time'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'crap.workshop',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workshop_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('crap.workshop');
    $form['proposed_workshops'] = [
      '#type' => 'textarea',
      '#title' => $this->t('proposed workshops'),
      '#description' => $this->t('Paste Proposed Workshops here'),
      '#default_value' => $config->get('proposed_workshops'),
    ];
    $form['actual_workshops'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comprehensive Workshop Calendar'),
      '#description' => $this->t('Enter the actual workshop calendar info here'),
      '#default_value' => $config->get('actual_workshops'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('crap.workshop')
      ->set('proposed_workshops', $form_state->getValue('proposed_workshops'))
      ->set('actual_workshops', $form_state->getValue('actual_workshops'))
      ->save();
  }

}
