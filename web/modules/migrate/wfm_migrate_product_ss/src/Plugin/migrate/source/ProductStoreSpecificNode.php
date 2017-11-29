<?php

/**
 * @file
 * Contains \Drupal\wfm_migrate_product_ss\Plugin\migrate\source\ProductStoreSpecificSNode.
 */

namespace Drupal\wfm_migrate_product_ss\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
//use DateTime;

/**
 * Source plugin for Product Store-Specific content.
 *
 * @MigrateSource(
 *   id = "product_ss_node"
 * )
 */
class ProductStoreSpecificNode extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /**
     * An important point to note is that your query *must* return a single row
     * for each item to be imported. We simply query
     * the base node data here, and pull in the relationships in prepareRow()
     * below.
     */
    $query = $this->select('storeinfo', 's')
      ->fields('s', [
        'ssid',
        'identifier',
        'tlc',
        'currency',
        'retailuom',
        'price',
        'pricemultiple',
        'saleprice',
        'salepricemultiple',
        'salestart',
        'saleend',
        'available',
      ])
      ->orderBy('ssid');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'ssid' => $this->t('Product ID-TLC Unique field'),
      'identifier' => $this->t('Product ID'),
      'tlc' => $this->t('Three Letter Code'),
      'currency' => $this->t('Currency'),
      'retailuom' => $this->t('Retail Unit of Measure'),
      'price' => $this->t('Price'),
      'pricemultiple' => $this->t('Price Multiple'),
      'salestart' => $this->t('Sale Start Date'),
      'saleend' => $this->t('Sale End Date'),
      'available' => $this->t('Available'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'ssid' => [
        'type' => 'string',
        'alias' => 's',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    /**
     * Let's go get the subTeam taxonomy tid for each product
     */
    $start = $row->getSourceProperty('salestart');
    if (!is_null($start)) {
//      drupal_set_message('start:' . $start) ;
      $timestamp = strtotime($start);
      $start = date("Y-m-d\TH:i:s", $timestamp);
//      drupal_set_message('reformatted start: ' . $start);
      $row->setSourceProperty('salestart',$start);
    }

    $end = $row->getSourceProperty('saleend');
    if (!is_null($end)) {
//      drupal_set_message('end:' . $end);
      $timestamp = strtotime($start);
      $end = date("Y-m-d\TH:i:s", $timestamp);
//      drupal_set_message('reformatted end: ' . $end);
      $row->setSourceProperty('saleend',$end);
    }

    // Fill in the field_product_ref entity ref to the product node.
    $id = $row->getSourceProperty('identifier');
    $product_ref_id = $this->lookupProductNid($id);
    $row->setSourceProperty('product_ref_id',$product_ref_id);

    $tlc = $row->getSourceProperty('tlc');
    $store_ref_id = $this->lookupStoreNid($tlc);
    $row->setSourceProperty('store_ref_id',$store_ref_id);

    $ssid = $row->getSourceProperty('ssid');
    drupal_set_message('Processing: ' . $ssid);
    /**
     * As explained above, we need to pull the style relationships into our
     * source row here, as an array of 'style' values (the unique ID for
     * the beer_term migration).
     */
//    $terms = $this->select('migrate_example_beer_topic_node', 'bt')
//      ->fields('bt', ['style'])
//      ->condition('bid', $row->getSourceProperty('bid'))
//      ->execute()
//      ->fetchCol();
//    $row->setSourceProperty('terms', $terms);

    // As we did for favorite beers in the user migration, we need to explode
    // the multi-value country names.
//    if ($value = $row->getSourceProperty('countries')) {
//      $row->setSourceProperty('countries', explode('|', $value));
//    }
    return parent::prepareRow($row);
//  }
  }
  public function lookupProductNid($id) {
    if (!strlen($id)) {
      return NULL;
    }
    $eid = db_query('SELECT f.entity_id
      FROM {node__field_product_identifier} f
      WHERE f.field_product_identifier_value = :id', array(':id' => $id))
      ->fetchField();
    if ($eid === false) {
      $eid = NULL;
    }
    return $eid;
  }

  /**
   *
   * Find the matching store nid for the tlc param.
   *
   * @param $tlc
   * @return entity id
   */
  public function lookupStoreNid($tlc) {
    if (!strlen($tlc)) {
      return NULL;
    }
    $eid = db_query('SELECT f.entity_id
      FROM {node__field_store_tlc} f
      WHERE f.field_store_tlc_value = :tlc', array(':tlc' => $tlc))
      ->fetchField();
    if ($eid === false) {
      $eid = NULL;
    }
    return $eid;
  }


}
