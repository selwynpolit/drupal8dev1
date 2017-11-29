<?php

/**
 * @file
 * Contains \Drupal\wfm_migrate_locations\Plugin\migrate\source\LocationTerm.
 */

namespace Drupal\wfm_migrate_locations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for Product content.
 *
 * @MigrateSource(
 *   id = "location_term"
 * )
 */
class LocationTerm extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /**
     * An important point to note is that your query *must* return a single row
     * for each item to be imported. We simply query
     * the base node data here, and pull in the relationships in prepareRow()
     * below.
     *
     * select d.tid,d.vid,d.name, h.parent
    from d7_taxonomy_term_data d
    inner join d7_taxonomy_term_hierarchy h
    on d.tid=h.tid
    where d.vid=51
    order by tid
     */
    $query = $this->select('d7_taxonomy_term_data', 'd')
      ->fields('d', array('tid','vid','name','description'))
      ->fields('h',array(parent))
      ->condition('d.vid','51','=');
    $query->orderBy('d.tid');
    $query->join('d7_taxonomy_term_hierarchy','h', 'd.tid=h.tid');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'tid' => $this->t('Taxonomy ID'),
      'vid' => $this->t('Vocabulary ID'),
      'name' => $this->t('Term Name'),
      'Description' => $this->t('Description'),
      'parent' => $this->t('Parent ID'),
      'size' => $this->t('Size'),
      'uom' => $this->t('Unit of Measure'),
      'tlc' => $this->t('Three Letter Code'),
      'store_name' => $this->t('Store Name'),
      'business_unit'=> $this->t('Business Unit'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'tid' => [
        'type' => 'string',
        'alias' => 'd',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    //Let's go get the tid for each parent.
    $parent = $row->getSourceProperty('parent');
    $parent_tid = $this->lookupParentId($parent);
    if ($parent_tid) {
      $row->setSourceProperty('parent',$parent_tid);
    }
    //Now we need the TLC and Store name and set the business unit.
    $name = $row->getSourceProperty('name');
    $name_length = strlen($name) ;
    if ($name_length == 2) {
      $row->setSourceProperty('business_unit', 'National Office');
    }
    else if ($name_length == 3) {
      $row->setSourceProperty('business_unit','Whole Foods Market');
    }
    else if ($name_length > 3) {
      //Chicago (Metro).
      if (strpos($name, "(Metro)")) {
        $row->setSourceProperty('business_unit','Metro');
      }
      else if ($name == "UK (United Kingdom)") {
        $row->setSourceProperty('business_unit','National Office');
      }
      else if ($name == "BC (Vancouver)") {
        $row->setSourceProperty('business_unit','Metro');
      }
      else if ($name == "Ann Arbor") {
        $row->setSourceProperty('business_unit','Metro');
      }
      else if ($name == "CA (Canadian National Office)") {
        $row->setSourceProperty('business_unit','National Office');
      }
      // Region such as: CE (Central)
      else if (substr($name,3,1)=="(") {
        $row->setSourceProperty('business_unit','Region');
      }
      else if (strpos($name,"(")) {
        $tlc = substr($name,0,3);
        $row->setSourceProperty('tlc',$tlc);
        $store_name = substr($name, 5, $name_length-6);
        drush_print_r($store_name);
        $row->setSourceProperty('store_name',$store_name);
        $row->setSourceProperty('business_unit','Whole Foods Market');
      }
      else {
        $row->setSourceProperty('business_unit','Metro');
      }
    }
    return parent::prepareRow($row);
  }
  public function lookupParentId($tid) {
    if (!strlen($tid)) {
      return NULL;
    }
    //Look in d7_taxonomy_term_data for the name of this tid.
    $name = db_query('SELECT t.name
      FROM {d7_taxonomy_term_data} t
      WHERE t.tid = :tid', array(':tid' => $tid))
      ->fetchField();
    if ($name === false) {
      //No parent found!
      return NULL;
    }

    //Then look for the d8 tid matching this name in {taxonomy_term_data}.
    $parent_tid = db_query('SELECT td.tid
      FROM {taxonomy_term_field_data} td
      WHERE td.name = :name', array(':name' => $name))
      ->fetchField();
    if ($parent_tid === false) {
      return NULL;
    }
    return $parent_tid;
  }
}
