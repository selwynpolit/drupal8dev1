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
    $form['proposed_workshops'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Proposed workshops'),
      '#rows' => 4,
      '#resizable' => 'both',
      '#description' => $this->t('Paste Proposed Workshops here'),
      '#required' => TRUE,
    ];
    $form['scheduled_workshops'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Scheduled workshops'),
      '#rows' => 4,
      '#resizable' => 'both',
      '#description' => $this->t('Paste Scheduled Workshops here'),
      '#required' => TRUE,
    ];

    $form['remove_workshops'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove all existing workshops first.'),
      '#default_value' => 1,
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
        if (count($current_workshop) >= 4) {
          $workshops[] = $current_workshop;
        }
        $current_workshop = [];
      }
    }
    return $workshops;

  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if ($form['remove_workshops']['#value']) {
      $result = \Drupal::entityQuery("node")
        ->condition('type', 'workshop')
        ->execute();
    }

    $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
    $entities = $storage_handler->loadMultiple($result);
    $storage_handler->delete($entities);

    // Build an array of proposed workshops and store them in the db.
    $proposed_workshops = $this->buildWorkshopArray($form['proposed_workshops']['#value']);
    $this->storeWorkshops($proposed_workshops, "Proposed");
    dsm("Processed " . count($proposed_workshops) . " proposed workshops.");

    // Build an array of scheduled workshops and store them in the db.
    $scheduled_workshops = $this->buildWorkshopArray($form['scheduled_workshops']['#value']);
    dsm("Processed " . count($scheduled_workshops) . " scheduled workshops.");
    $this->storeWorkshops($scheduled_workshops, "Scheduled");

  }

  /**
   * Store the workshop info in the drupal db.
   *
   * @param array $workshops
   *   Workshops object.
   * @param string $wsType
   *   Type of workshop.
   */
  private function storeWorkshops(array $workshops, $wsType = "Proposed") {
    foreach ($workshops as $workshop) {
      $ws = new WorkshopEvent($workshop, $wsType);
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
