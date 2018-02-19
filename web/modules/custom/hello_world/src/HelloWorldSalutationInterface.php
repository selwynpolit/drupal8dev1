<?php
namespace Drupal\hello_world;

interface HelloWorldSalutationInterface {

  /**
   * Returns a the Salutation render array.
   */
  public function getSalutationComponent();

  /**
   * Returns the salutation
   */
  public function getSalutation();


  public function makeLink();


}