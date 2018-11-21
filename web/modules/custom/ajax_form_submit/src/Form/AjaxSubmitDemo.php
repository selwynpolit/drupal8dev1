<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 2/16/18
 * Time: 7:51 PM
 */

namespace Drupal\ajax_form_submit\Form;


use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AjaxSubmitDemo extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'ajax_submit_demo';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    //Move this element somewhere else if you want the result message elsewhere.
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>'
    ];


   $form['number_1'] = [
     '#type' => 'textfield',
     '#title' => $this->t('Number 1'),
   ];

    $form['number_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number 2'),
    ];

    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::setMessage',
      ],
    ];

    $form['message2'] = [
      '#type' => 'markup',
      '#markup' => '<div class="other_message"></div>'
    ];


    return $form;

  }

  /**
   * just have to put it here even tho it never gets used...
   *
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * This gets called on submit
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function setMessage(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result_message',
        '<div class="my_top_message">' . t('The results is ') .
        ($form_state->getValue('number_1') + $form_state->getValue('number_2'))
        . '</div>')
    );

    /* The first parameter of the HtmlCommand instance is actually the class of
     * our markup element that we created in our form (.result_message).
    */

    //Add a second message for grins.
    $response->addCommand(
      new HtmlCommand(
        '.other_message',
        '<div class="my_other_message">' . t('Good job dude!') . '</div>')
    );

    return $response;
  }


}