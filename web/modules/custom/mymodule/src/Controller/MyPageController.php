<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 11/28/17
 * Time: 2:19 PM
 */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class MyPageController
 *
 * @package Drupal\mymodule\Controller
 *
 * Returns responses for mymodule module
 */
class MyPageController extends ControllerBase {

  /**
   * Returns markup for our custom page.
   *
   * @return array
   */
  public function customPage() {
    return [
      '#markup' => t('Welcome to yur custom page on toasted!'),
    ];
  }
}

