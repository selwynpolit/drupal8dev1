<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 3/15/18
 * Time: 5:11 PM
 */

/**
 * @file
 * Contains \Drupal\rsvplist\Form\RSVPConfigurationForm
 */

namespace Drupal\rsvp\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\NodeType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RSVPConfigurationForm
 *
 * @package Drupal\rsvp\Form
 */
class RSVPConfigurationForm extends ConfigFormBase {

  /**
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return ['rsvp.settings'];
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'rsvp_admin_settings';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
//    $types = node_types_get_names();

    $config = $this->config('rsvp.settings');

    // Load up list of node types
    $node_types = NodeType::loadMultiple();
    $options = [];
    foreach ($node_types as $node_type) {
      $options[$node_type->id()] = $node_type->label();
    }

    $form['rsvp_node_types'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('The content types top enable rSVP collection for'),
      '#default_value' => $config->get('allowed_types'),
      '#options' => $options,
      '#description' => $this->t('On the specified node types, an RSVP option will be available and can be enabled while that node is being edited.'),
    );
    $form['array_filter'] = array('#type' => 'value', '#value' => TRUE);
    return parent::buildForm($form, $form_state);
  }


  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $allowed_types = array_filter($form_state->getValue('rsvp_node_types'));
    sort($allowed_types);
    $this->config('rsvp.settings')
      ->set('allowed_types', $allowed_types)
      ->save();

    parent::submitForm($form, $form_state);
  }
}