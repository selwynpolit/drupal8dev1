<?php

/**
 * @file
 * Contains \Drupal\wfm_migrate_recipe_photo\Plugin\migrate\source\WfmMigrateProductPic.
 */

namespace Drupal\wfm_migrate_product_pic\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;


/**
 * Source plugin for WFM Recipe content.
 *
 * @MigrateSource(
 *   id = "product_pic"
 * )
 */
class WfmMigrateProductPic extends SqlBase {

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
    $query = $this->select('images', 'i')
      ->fields('i', [
        'iid',
        'identifier',
        'url',
        'angle',
      ])
      ->orderBy('url');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'iid' => $this->t('Image ID'),
      'identifier' => $this->t('Product ID'),
      'url' => $this->t('Image URL'),
      'angle' => $this->t('Angle of Image'),
      'filename_with_path' => $this->t('Drupal dest file path with public://dir/ inserted')
    ];
    return $fields;
  }



  /**
   *
   * Identifies the field that is the unique id per row
   *
   * @return array
   */
  public function getIds() {
    return [
      'identifier' => [
        'type' => 'string',
        'alias' => 'i',
      ],
    ];
  }



  public function prepareRow(Row $row) {
    // do my row mods here..

    // Bail if this is a non-published recipe
    //$status = $row->getSourceProperty("status");
    //if ($status != 'Published') {
    //  return FALSE;
    //}

    //$filename = $row->getSourceProperty("filename") ;
    //$str = sprintf("Processing: %s", $filename);
    //drush_print_r($str);

    $url = $row->getSourceProperty("url") ;
    $str = sprintf("Processing: %s", $url);
    drush_print_r($str);
    $filename_with_path = basename($url);
    $filename_with_path = 'public://product-images/' . $filename_with_path;
    drush_print_r($filename_with_path);
    $row->setSourceProperty('filename_with_path', $filename_with_path);


    return parent::prepareRow($row);
  }
}
