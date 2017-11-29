<?php
/**
 *
 */
namespace Drupal\wfm_store_select\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity;
//use Drupal\Core\Session\AccountInterface;
//use \Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class WfmStoreSelectController extends ControllerBase {


  /**
   * This will return the output of the foobar page.
   */
  public function selectMe( $tlc) {

    // Write this to field_user_tlc in profile for logged in user.
    $account = \Drupal::currentUser();
    $id = $account->id();
    $user = \Drupal\user\Entity\User::load($id);
    if ($id > 0) {

      $current_tlc = $user->get('field_user_tlc');
      $user->set('field_user_tlc', $tlc);
      $user->save();
      //\Drupal\user\Entity\User::save($user);

      //$account->field_user_tlc = $tlc ;
      //$account->save();

      $str = $tlc . " Store TLC has been stored to your field_user_tlc in your profile. ";

      return array(
        //'#markup' => t('Store TLC(three letter code): has been stored to your field_user_tlc in your profile.'),
        '#markup' => t($str),
      );
    //} else {
    //  return new RedirectResponse(\Drupal::url('user.page'));
      /**
       * here we want to lookup the store nid from the TLC
       * and redirect to the store
       */


    }
  }
}