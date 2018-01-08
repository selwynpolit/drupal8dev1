<?php

namespace Drupal\workshops\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\workshops\selwyn\WorkshopEvent;

/**
 * Class WorkshopForm.
 *
 * @package Drupal\workshops\Form
 */
class WorkshopForm extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'workshop_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $proposed_workshops = \Drupal::state()->get('workshops.proposed_workshops');
    $default_proposed_workshops = "";
    foreach ($proposed_workshops as $workshop) {
      $default_proposed_workshops = $default_proposed_workshops.implode("\n", $workshop);
    }

    $default_scheduled_workshops = "";
    $scheduled_workshops = \Drupal::state()->get('workshops.scheduled_workshops');
    foreach ($scheduled_workshops as $workshop) {
      $default_scheduled_workshops = $default_scheduled_workshops.implode("\n", $workshop);
    }

    $form['proposed_workshops'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Proposed workshops'),
      '#rows' => 4,
      '#resizable' => 'both',
      '#description' => $this->t('Paste Proposed Workshops here'),
      '#default_value' => $default_proposed_workshops,
//      '#default_value' => \Drupal::state()->get('workshops.proposed_workshops'),
      '#required' => FALSE,
    ];
    $form['scheduled_workshops'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Scheduled workshops'),
      '#rows' => 4,
      '#resizable' => 'both',
      '#description' => $this->t('Paste Scheduled Workshops here'),
      '#default_value' => $default_scheduled_workshops,
      '#required' => FALSE,
    ];

    $form['remove_workshops'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove all existing workshops first.'),
      '#default_value' => 0,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;

  }

  /**
   * Split string into an array of lines.
   *
   * @param string $str
   *   The string.
   *
   * @return array
   *   The array of lines.
   */
  private function buildWorkshopArray(string $str) {
    $lines = preg_split("/\\r\\n|\\r|\\n/", $str);
    $workshops = [];
    $current_workshop = [];

    foreach ($lines as $line) {
      if (!empty($line)) {
        $current_workshop[] = $line;
      }
      else {
        // Blank line indicates we're probably on a new workshop
        if (count($current_workshop) >= 4) {
          $workshops[] = $current_workshop;
        }
        $current_workshop = [];
      }

    }
    //If there were no blank lines, there could be a workshop left over here.
    if (count($current_workshop)) {
      $workshops[] = $current_workshop;
    }
    return $workshops;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Delete existing workshop nodes if this item is checked.
    if ($form['remove_workshops']['#value']) {
      $result = \Drupal::entityQuery("node")
        ->condition('type', 'workshop')
        ->execute();
      $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
      $entities = $storage_handler->loadMultiple($result);
      $storage_handler->delete($entities);
    }


    // Build an array of proposed workshops and store them in the db.
    $proposed_workshops = $this->buildWorkshopArray($form['proposed_workshops']['#value']);
    if (!empty($proposed_workshops)) {
      //Save the values so they can appear next time.
      \Drupal::state()->set('workshops.proposed_workshops', $proposed_workshops);
      $this->storeWorkshops($proposed_workshops, "Proposed");
      dsm("Processed " . count($proposed_workshops) . " proposed workshops.");
    }

    // Build an array of scheduled workshops and store them in the db.
    $scheduled_workshops = $this->buildWorkshopArray($form['scheduled_workshops']['#value']);
    if (!empty($scheduled_workshops)) {
      //Save the values so they can appear next time.
      \Drupal::state()->set('workshops.scheduled_workshops', $scheduled_workshops);
      dsm("Processed " . count($scheduled_workshops) . " scheduled workshops.");
      $this->storeWorkshops($scheduled_workshops, "Scheduled");
    }

  }

  /**
   * Store the workshop info in the drupal db.
   *
   * @param array $workshops
   *   Array of workshop arrays.
   * @param string $wsType
   *   Type of workshop.
   */
  private function storeWorkshops(array $workshops, $wsType = "Proposed") {
    foreach ($workshops as $workshop) {
      $ws = new WorkshopEvent($workshop, $wsType);

      //Process and Validate the leaders
      $rc = $ws->processLeaders();

      $wsData = [];
      $rc = $ws->getNodeReady($wsData);
      if ($rc) {

        $wsNode = Node::create($wsData);
        $wsNode->save();
        // dsm($ws->getTitle());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation is optional.
    parent::validateForm($form, $form_state);

    if (!$form_state->isValueEmpty('proposed_workshops')) {
      if (strlen($form_state->getValue('proposed_workshops')) <= 20) {
        $form_state->setErrorByName('proposed_workshops', t('Proposed workshops seems too short (<21 chars)'));
      }
    }

  }

}
