<?php

namespace Drupal\drupalform\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ExampleForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'drupalform_example_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['company_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Company name'),
    ];
    $form['integer'] = [
      '#type' => 'number',
      '#title' => $this->t('Some integer'),
      // The increment or decrement amount
      '#step' => 1,
      // Miminum allowed value
      '#min' => 0,
      // Maxmimum allowed value
      '#max' => 100,
    ];
    $form['company_address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
    ];
    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
    ];
//    $form['machine_name'] = [
//      '#type' => 'machine_name',
//      '#title' => $this->t('Machine name'),
//    ];
//    $form['starting_date'] = [
//      '#type' => 'date',
//      '#title' => $this->t('Starting date'),
//      '#date_format' => 'Y-m-d',  //no workee??
////      '#default_value' => array('year' => 2020, 'month' => 2, 'day' => 15,),
//      '#required' => TRUE,
//    ];
    $form['company_telephone'] = [
      '#type' => 'tel',
//      '#weight' => 25,
      '#title' => $this->t('Company phone'),
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
    ];
    $form['range'] = [
      '#type' => 'range',
      '#title' => $this->t('Range'),
      '#min' => 0,
      '#max' => 100,
      '#step' => 1,
    ];
//    $form['search'] = [
//      '#type' => 'search',
//      '#title' => $this->t('Search'),
//      '#autocomplete_route_name' => FALSE,
//    ];
    $form['website'] = [
      '#type' => 'url',
      '#title' => $this->t('Website'),
    ];

    $giftOption = 1;
    $form['gift'] = [
      '#type' => 'radios',
      '#title' => $this->t('Is this a gift?'),
      '#default_value' => $giftOption,
      '#options' => [
        0 => $this->t('Yes'),
        1 => $this->t('No'),
      ],
    ];

    $colorOption = 1;
    $form['color'] = [
      '#type' => 'radios',
      '#title' => $this->t('What color?'),
      '#default_value' => $colorOption,
      '#options' => [
        0 => $this->t('Red'),
        1 => $this->t('Green'),
        2 => $this->t('Blue'),
      ],
    ];


    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $formState) {
    if (!$formState->isValueEmpty('company_name')) {
      if (strlen($formState->getValue('company_name')) <= 5) {
        //Set validation error.
        $formState->setErrorByName('company_name', t('Company name is less than 5 characters'));
      }
    }
  }


}