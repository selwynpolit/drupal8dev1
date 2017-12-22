<?php

namespace Drupal\workshops\selwyn;


class WorkshopEvent {

  private $start_date;
  private $end_date;
  private $location_string;
  private $country;
  private $title;
  private $leader;
  private $audience;
  private $original_array;
  private $type;



  public function __construct($ws, $wsType) {
    $this->original_array = $ws;
    $this->buildDates($ws[0]);
    $this->type = $wsType;

    //6 liners are arranged differently
    if (count($ws) == 6) {
      //mungle 1, 2, 3 until I can get smarter.
      $this->buildLocation($ws[1] . ' ' . $ws[2]);
      $this->buildCountry($ws[1] . ' ' . $ws[2]);
      $this->buildTitle($ws[1] . ' ' . $ws[2] . ' ' . $ws[3]);
      $this->buildLeader($ws[4]);
      $this->buildAudience($ws[5]);

    } else {
      //5 and 4 liners
      $this->buildLocation($ws[1]);
      $this->buildCountry($ws[1]);
      $this->buildTitle($ws[2]);
      $this->buildLeader($ws[3]);
      if (array_key_exists(4, $ws)){
        $this->buildAudience($ws[4]);
      }
    }
  }

  public function getNodeReady(&$node_data) {

    if (!is_a($this->start_date, 'DateTime')) {
      devel_set_message("invalid start date for " . $this->title, 'error');
      return FALSE;
    }
    if (!is_a($this->end_date, 'DateTime')) {
      devel_set_message("invalid end date for " . $this->title, 'error');
      return FALSE;
    }

    $node_data = [
      'type' => 'workshop',
      'title' => $this->title,
      'field_workshop_type' => $this->type,
      'field_workshop_location' => $this->location_string,
      'field_workshop_leader' => $this->leader,
      'field_workshop_audience' => $this->audience,
      'field_workshop_country' => $this->country,
      'field_workshop_start_date' => $this->start_date->format('Y-m-d'),
      'field_workshop_end_date' => $this->end_date->format('Y-m-d'),
      'field_workshop_original_posting' => implode(chr(13),$this->original_array),
      //        'field_workshop_type' => 'Proposed',
    ];
    return TRUE;
  }

  public function buildCountry($str) {
    if (stripos($str, "USA")) {
      $this->country = "USA";
    }
    elseif (stripos($str, "North America")) {
      $this->country = "USA";
    }
    elseif (stripos($str, "England")) {
      $this->country = "England";
    }
    elseif (stripos($str, "Netherlands")) {
      $this->country = "The Netherlands";
    }
    elseif (stripos($str, "Australia")) {
      $this->country = "Australia";
    }
    elseif (stripos($str, "Switzerland")) {
      $this->country = "Switzerland";
    }
    elseif (stripos($str, "Taiwan")) {
      $this->country = "Taiwan";
    }
    elseif (stripos($str, "Denmark")) {
      $this->country = "Denmark";
    }
    elseif (stripos($str, "Swaziland")) {
      $this->country = "Swaziland";
    }
    elseif (stripos($str, "Poland")) {
      $this->country = "Poland";
    }
    else {
      $this->country = NULL;
    }
  }


  /**
   * @return mixed
   */
  public function getTitle() {
    return $this->title;
  }

  public function buildLocation(string $str) {
    $this->location_string = $str;
  }

  public function buildTitle(string $str) {
    $this->title = $str;
  }

  public function buildLeader(string $str) {
    $this->leader = $str;
  }

  public function buildAudience(string $str) {
    $this->audience = $str;
  }

  public function buildDates($str) {
    //grab the month word
    $rc = preg_match("/^\w+/", trim($str), $output_array);
    if ($rc) {
      $month = $output_array[0];
    }
    //first part of date i.e. Feb 16-18 - should return the 16
    $rc = preg_match("/\d+/", $str, $output_array);
    if ($rc) {
      $first_day = $output_array[0];
    }
    $rc = preg_match("/(\d+)(\/)/", $str, $output_array);
    if ($rc) {
      $second_day = $output_array[1];
    }
    //sometimes there's a second month.  e.g. August 31-September 3/18
    $second_month = "";
    $rc = preg_match("/(\w+) (\d+)(\/)/", $str, $output_array);
    if ($rc) {
      // Is this a valid month?
      $x = \DateTime::createFromFormat('M', $output_array[1]);
      if ($x) {
        $second_month = $output_array[1];
      }
    }

    $rc = preg_match("/(\/)(\d+)/", $str, $output_array);
    if ($rc) {
      $year = "20" . $output_array[2];
    }

    $date_str = $first_day . '-' . $month . '-' . $year . '00:00:00';
    $this->start_date = \DateTime::createFromFormat('j-M-Y H:i:s', $date_str);

    $month = (!empty($second_month) ? $second_month : $month);
    $date_str = $second_day . '-' . $month . '-' . $year . '00:00:00';
    $this->end_date = \DateTime::createFromFormat('j-M-Y H:i:s', $date_str);

  }
}

