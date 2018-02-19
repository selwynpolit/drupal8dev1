<?php

namespace Drupal\mymodule\Plugin\Block;

use Drupal\Core\block\BlockBase;
use \Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;


/**
 * @Block(
 *   id = "copyright_block",
 *   admin_label = @Translation("Copyright"),
 *   category = @Translation("Custom")
 * )
 */

class Copyright extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $date = new \DateTime();
    return [
      '#markup' => t('Copyright @year&copy; @company <br/>@time', [
        '@year' => $date->format('Y'),
        '@time' => $date->format('Y-m-d H:i:s'),
        '@company' => $this->configuration['company_name'],
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'company_name' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['company_name'] = [
      '#type' => 'textfield',
      '#title' => t('Company name'),
      '#default_value' => $this->configuration['company_name'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['company_name'] =
      $form_state->getValue('company_name');
  }


  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $route_name = \Drupal::routeMatch()->getRouteName();

    // not on the user login and logout pages
    if (!in_array($route_name,array('user.login', 'user.logout'))) {
      return AccessResult::allowed();
    }

    //Auth user
    if ($account->isAuthenticated()) {
      return AccessResult::allowed();
    }
    //Anon.
    if ($account->isAnonymous()) {
      return AccessResult::forbidden();
    }



    return AccessResult::forbidden();
  }


}