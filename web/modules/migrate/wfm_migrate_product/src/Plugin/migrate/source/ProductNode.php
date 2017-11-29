<?php

/**
 * @file
 * Contains \Drupal\wfm_migrate_product\Plugin\migrate\source\ProductNode.
 */

namespace Drupal\wfm_migrate_product\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for Product content.
 *
 * @MigrateSource(
 *   id = "product_node"
 * )
 */
class ProductNode extends SqlBase {

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
    $query = $this->select('products', 'p')
      ->fields('p', [
        'identifier',
        'brand',
        'description',
        'subteamnumber',
        'subteam',
        'size',
        'uom'
      ])
      ->orderBy('identifier');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'identifier' => $this->t('Product ID'),
      'brand' => $this->t('Product Brand'),
      'description' => $this->t('Description of the product'),
      'subteam' => $this->t('Subteam'),
      'subteamnumber' => $this->t('Subteam Number'),
      'size' => $this->t('Size'),
      'uom' => $this->t('Unit of Measure'),
      'images' => $this->t('Images'), // Note that this field is not part of the query above - it is populated
      // by prepareRow() below.
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'identifier' => [
        'type' => 'string',
        'alias' => 'p',
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
    $subteamnumber = $row->getSourceProperty('subteamnumber');
    $row->setSourceProperty('subTeamnumber',$subteamnumber);

    $id = $row->getSourceProperty('identifier');
    drush_print_r($id);

    $fids = array();
    $results = $this->select('images','i')
      ->fields('i',array('identifier','url','angle'))
      ->orderby('angle','DESC')
      ->condition('identifier',$row->getSourceProperty('identifier'))
      ->execute();
    foreach ($results as $result) {
      $pic = basename($result['url']);
      drush_print_r($pic);
      drush_print_r($result['angle']);
      $fids[] = $this->lookupImageFid($pic);
    }
    if (count($fids)) {
//      if (count($fids)>1) {
//        drupal_set_message("fids >1\n");
//      }
      $row->setSourceProperty('images',$fids);
    }
    return parent::prepareRow($row);
//  }
  }
  public function lookupImageFid($image) {
    if (!strlen($image)) {
      return NULL;
    }
    $fid = db_query('SELECT f.fid
      FROM {file_managed} f
      WHERE f.filename = :filename', array(':filename' => $image))
      ->fetchField();
    if ($fid === false) {
      $fid = NULL;
    }
    return $fid;
  }
}
