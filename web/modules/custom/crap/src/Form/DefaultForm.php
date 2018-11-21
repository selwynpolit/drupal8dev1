<?php

namespace Drupal\crap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultForm.
 */
class DefaultForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'crap.default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'default_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('crap.default');
    $form['text1'] = [
      '#type' => 'textarea',
      '#title' => $this->t('text1'),
      '#description' => $this->t('text1'),
      '#default_value' => $config->get('text1'),
    ];
    $form['text2'] = [
      '#type' => 'textarea',
      '#title' => $this->t('text2'),
      '#description' => $this->t('text2'),
      '#default_value' => $config->get('text2'),
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

    $this->config('crap.default')
      ->set('text1', $form_state->getValue('text1'))
      ->set('text2', $form_state->getValue('text2'))
      ->save();
  }

}
