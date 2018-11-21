<?php

namespace Drupal\fieldstuff\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'realname_one_line' formatter.
 *
 * @FieldFormatter(
 *   id = "realname_one_line",
 *   label = @Translation("Real name (one line)"),
 *   field_types = {
 *     "realname"
 *   }
 * )
 *
 */
class RealNameFormatter extends FormatterBase {

  /**
   * @inheritDoc
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $this->t('@first @last', [
          '@first' => $item->first_name,
          '@last' => $item->last_name,
        ]),
      ];
    }
    return $element;
  }
}