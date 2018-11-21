<?php
/**
 * @file
 *
 * Contains \Drupal\rsvp\EnablerService
 */

namespace Drupal\rsvp;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

/**
 * Class EnablerService
 *
 * @package Drupal\rsvp
 */
class EnablerService {

  /**
   * EnablerService constructor.
   */
  public function __construct() {


  }

  /**
   * Sets an indiv node to be RSVP enabled.
   *
   * @param \Drupal\node\Entity\Node $node
   *
   * @throws \Exception
   */
  public function setEnabled(Node $node) {
    if (!$this->isEnabled($node)) {
      $insert = Database::getConnection()->insert('rsvplist_enabled');
      $insert->fields(['nid'], [$node->id()]);
      $insert->execute();
    }
  }


  /**
   * Checks if an indiv node is RSVP enabled.
   *
   * @param \Drupal\node\Entity\Node $node
   *
   * @return bool
   *   whether the node is enabled for the RSVP functionality.
   */
  public function isEnabled(Node $node) {
    if ($node->isNew()) {
      return FALSE;
    }
    $select = Database::getConnection()->select('rsvplist_enabled', 're');
    $select->fields('re', ['nid']);
    $select->condition('nid', $node->id());
    $results = $select->execute();
    return !empty(($results->fetchCol()));
  }

  /**
   * Delete enabled settings for an indiv node.
   *
   * @param \Drupal\node\Entity\Node $node
   */
  public function delEnabled(Node $node) {
    $delete = Database::getConnection()->delete('rsvplist_enabled');
    $delete->condition('nid', $node->id());
    $delete->execute();
  }

}