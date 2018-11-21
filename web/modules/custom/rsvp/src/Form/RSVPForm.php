<?php
/**
 * @file
 * Contains \Drupal\rsvplist\Frm\RSVPForm
 */


namespace Drupal\rsvp\Form;

use Drupal\Core\Cache\DatabaseBackend;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\user\Entity\User;

/**
 * Class RSVPForm
 *
 * @package Drupal\rsvp\Form
 */
class RSVPForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'rsvplist_email_form';
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

    // Current node.
    $nid = NULL ;
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node) {
      $nid = $node->id;
    }
    $form['email'] = [
      '#title' => t('Email Address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t("We'll send updates to the email address"),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if ($value == !\Drupal::service('email.validator')->isValid($value)) {
      $form_state->setErrorByName('email', t('The email %mail is not valid.', ['%mail'=> $value]));
      return;
    }

    //Check if the email is already set for this node.
    $node = \Drupal::routeMatch()->getParameter('node');
    $select = Database::getConnection()->select('rsvplist', 'r');
    $select->fields('r', ['nid']);
    $select->condition('nid', $node->id());
    $select->condition('mail', $value);

    parent::validateForm($form, $form_state);
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
//    drupal_set_message(t("The form is working"));

    //load a proxy (subset of a real user object w/o fields etc.)
    $user = \Drupal::currentUser();

    //load a real user entity
    $user = User::load(\Drupal::currentUser()->id());

    db_insert('rsvplist')
      ->fields([
        'mail' => $form_state->getValue('email'),
        'nid' => $form_state->getValue('nid'),
        'uid' => $user->id(),
        'created' => time(),
      ])
      ->execute();


    drupal_set_message(t("Thank-you for your RSVP, you are on the list for the event."));
  }

}