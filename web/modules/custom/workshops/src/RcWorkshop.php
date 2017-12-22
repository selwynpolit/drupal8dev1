<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 12/19/17
 * Time: 4:52 PM
 */

namespace Drupal\WorkshopHandler\RcWorkshop;


class RcWorkshop {

  /**
   * @var Drupal\Core\Datetime
   */
  protected $start_date;

  /**
   * @var Drupal\Core\Datetime
   */
  protected $end_date;


  protected $location;
  protected $city;
  protected $state;
  protected $country;

  protected $target_audience;
  protected $title;
  protected $leader;


  /**
   * @param string $str
   */
  public function CreateWorkshop(string $str) {
    $str = "February 16-18/18 (formerly Feb. 23-26/18)
near Atlanta, Georgia, USA
Middle-Class Liberation
Seán Ruth
Central & Eastern North America
";

    $line_list = explode(PHP_EOL, $str);
    $line_list2 = preg_split("/\\r\\n|\\r|\\n/", $str);
    $dateline = $line_list[0];

    //grab the month word
    $rc = preg_match("/^\w+/", $line_list[0], $output_array);
    if ($rc) {
      $month = $output_array[0];
    }
    //first part of date i.e. Feb 16-18 - should return the 16
    $rc = preg_match("/\d+/", $line_list[0], $output_array);
    if ($rc) {
      $first_day = $output_array[0];
    }
    $rc = preg_match("/(\d+)(\/)/", $line_list[0], $output_array);
    if ($rc) {
      $second_day = $output_array[1];
    }

    $rc = preg_match("/(\/)(\d+)/", $line_list[0], $output_array);
    if ($rc) {
      $year = "20" . $output_array[2];
    }

    $str1 = $first_day . '-' . $month . '-' . $year . '00:00:00';
    $date1 = DateTime::createFromFormat('j-M-Y H:i:s',$str1);

  }

}