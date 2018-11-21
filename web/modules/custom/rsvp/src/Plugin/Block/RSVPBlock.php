<?php
/**
 * @file
 * contains Drupal\rsvp\Plugin\Block\RSVPBlock
 */

namespace Drupal\rsvp\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\rsvp\EnablerService;

//use Drupal\Core\Session\AccountInterface;
//use Drupal\Core\Access\AccessResult;

/**
 * Provides an 'RSVP' Block
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("RSVP Block"),
 *   category = @Translation("Custom")
 * )
 */
class RSVPBlock extends BlockBase {

  /**
   * @var \Drupal\rsvp\EnablerService
   */
  private $enablerService;

  /**
   * @inheritDoc
   */
  public function build() {
//    return [
//      '#markup' => $this->t('My RSVP List Block')
//    ];

    return \Drupal::formBuilder()->getForm('Drupal\rsvp\Form\RSVPForm');
  }

  /**
   * @inheritDoc
   */
  protected function blockAccess(AccountInterface $account) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node) {
      $nid = $node->id();
      if (is_numeric($nid)) {
        // See rsvp.permissions.yml for the permission string.
//        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');


        // Using a member.
        if (FALSE) {
          $this->enablerService = \Drupal::service('rsvp.enabler');
          if ($this->enablerService->isEnabled($node) == TRUE) {
            return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
          }

        }

        // or using a local var
        /** @var \Drupal\rsvp\EnablerService  $enabler*/
        $enabler = \Drupal::service('rsvp.enabler');
        if ($enabler->isEnabled($node) == TRUE) {
          return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
        }

      }
    }
    return AccessResult::forbidden();
//    return parent::blockAccess($account);
  }
}