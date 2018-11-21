<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 3/16/18
 * Time: 4:02 PM
 */

namespace Drupal\rsvp\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;


//use Drupal\Component\Render\HtmlEscapedText;
//use Drupal\Component\Utility\SafeMarkup;
//use Drupal\Component\Utility\Html;



class ReportController extends ControllerBase {

  /**
   * Get all RSVPs for all nodes.
   *
   * @return array
   */
  protected function getAllRSVPs() {
    $select = Database::getConnection()->select('rsvplist','r');

    // Join users table so we can get the entry creator's name.
    $select->join('users_field_data', 'u', 'r.uid=u.uid');

    // Join node table so we can get event's name.
    $select->join('node_field_data', 'n', 'r.nid=n.nid');

    $select->addField('u', 'name', 'username');
    $select->addField('n', 'title');
    $select->addField('r', 'mail');

    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $entries;
  }

  public function report() {
    $content = [];
    $content['message'] = [
      '#markup' => $this->t('Below is a list of all event RSVPs including username, email address and event they will be attending.'),
    ];
    $headers = [
      $this->t('Name'),
      $this->t('Event'),
      $this->t('Email')
    ];
    $rows = [];

    // shorthand
    //    foreach ($entries = $this->getAllRSVPs() as $entry) {


    $rows = $this->getAllRSVPs();
    foreach ($rows as $row) {
      // Don't sanitize.
//      $rows_array[] = $row;

      // Sanitize each entry.
      $rows_array[] = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $row);
//      $rows_array[] = array_map('Drupal\Component\Utility\Html::escape', $row);
    }

    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows_array,
      '#empty' => $this->t('No entries available.'),
    ];

    // Don't cache this page.
    $content['#cache']['max-age'] = 0;
    return $content;
  }

}