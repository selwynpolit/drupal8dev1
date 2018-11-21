<?php

namespace Drupal\fieldstuff\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'realname_last_first' formatter.
 *
 * @FieldFormatter(
 *   id = "realname_last_first",
 *   label = @Translation("Real name (last first with comma)"),
 *   field_types = {
 *     "realname"
 *   }
 * )
 *
 */
class RealNameLastFirstFormatter extends FormatterBase {

  /**
   * @inheritDoc
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => t('@last, @first', [
          '@last' => $item->last_name,
          '@first' => $item->first_name,
        ]),
      ];
    }
    return $element;
  }
}