<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 12/27/17
 * Time: 10:09 AM
 */

namespace Drupal\iai_pig\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\node\NodeInterface;


class DisplayProductImage extends ControllerBase {

  /**
   * @param \Drupal\node\NodeInterface $node
   *   The fuly loaded node entity.
   * @param $delta
   *   The image instance to load.
   *
   * @return array $render_array
   *   The render array.
   */
  public function displayProductImage(NodeInterface $node, $delta) {
    if (isset($node->field_product_image[$delta])) {
      $imageData = $node->field_product_image[$delta]->getValue();
      $file = File::load($imageData['target_id']);
      $render_array['image_data'] = array(
        '#theme' => 'image_style',
        '#uri' => $file->getFileUri(),
        '#style_name' => 'product_large',
        '#alt' => $imageData['alt'],
      );
    }
    else {
      $render_array = array(
        '#type' => 'markup',
        '#markup' => $this->t('You are viewing @title.  Unfortunately there is no image defined for delta: @delta.',
          array('@title' => $node->getTitle(), '@delta' =>$delta)),
      );
    }
    return $render_array;
  }

}