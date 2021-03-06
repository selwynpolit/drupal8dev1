<?php

namespace Drupal\webform\Element;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\webform\Utility\WebformArrayHelper;
use Drupal\webform\Utility\WebformOptionsHelper;

/**
 * Provides a webform element for a likert scale.
 *
 * @FormElement("webform_likert")
 */
class WebformLikert extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processWebformLikert'],
        [$class, 'processAjaxForm'],
      ],
      '#theme_wrappers' => ['form_element'],
      '#required' => FALSE,
      '#questions' => [],
      '#questions_description_display' => 'description',
      // Using #answers insteads of #options to prevent triggering
      // \Drupal\Core\Form\FormValidator::performRequiredValidation().
      '#answers' => [],
      '#answers_description_display' => 'description',
      '#na_answer' => FALSE,
      '#na_answer_text' => '',
      '#na_answer_value' => '',
    ];
  }

  /**
   * Processes a likert scale webform element.
   */
  public static function processWebformLikert(&$element, FormStateInterface $form_state, &$complete_form) {
    // Get answer with optional N/A.
    static::processWebformLikertAnswers($element);

    // Process answers.
    $answers = [];
    foreach ($element['#answers'] as $answer_key => $answer) {
      $answer = (string) $answer;
      if (strpos($answer, WebformOptionsHelper::DESCRIPTION_DELIMITER) === FALSE) {
        $answer_description_property_name = NULL;
        $answer_title = $answer;
        $answer_description = '';
      }
      else {
        $answer_description_property_name = ($element['#answers_description_display'] == 'help') ? 'help' : 'description';
        list($answer_title, $answer_description) = explode(WebformOptionsHelper::DESCRIPTION_DELIMITER, $answer);
      }
      $answers[$answer_key] = [
        'description_property_name' => $answer_description_property_name ,
        'title' => $answer_title,
        'description' => $answer_description,
      ];
    }

    // Build header.
    $header = [
      'likert_question' => ['question' => FALSE],
    ];
    foreach ($answers as $answer_key => $answer) {
      $header[$answer_key] = [
        'data' => [
          'title' => ['#markup' => $answer['title']],
        ],
      ];
      switch ($answer['description_property_name']) {
        case 'help':
          $header[$answer_key]['data']['help'] = [
            '#type' => 'webform_help',
            '#help' => $answer['description'],
          ];
          break;

        case 'description':
          $header[$answer_key]['data']['description'] = [
            '#type' => 'container',
            '#markup' => $answer['description'],
            '#attributes' => ['class' => ['description']],
          ];
          break;
      }
    }

    // Randomize questions.
    if (!empty($element['#questions_randomize'])) {
      $element['#questions'] = WebformArrayHelper::shuffle($element['#questions']);
    }

    // Build rows.
    $rows = [];
    foreach ($element['#questions'] as $question_key => $question) {
      $question = (string) $question;
      if (strpos($question, WebformOptionsHelper::DESCRIPTION_DELIMITER) === FALSE) {
        $question_description_property_name = NULL;
        $question_title = $question;
        $question_description = '';
      }
      else {
        $question_description_property_name = ($element['#questions_description_display'] == 'help') ? '#help' : '#description';
        list($question_title, $question_description) = explode(WebformOptionsHelper::DESCRIPTION_DELIMITER, $question);
      }

      $value = (isset($element['#value'][$question_key])) ? $element['#value'][$question_key] : NULL;
      $row = [];
      // Must format the label as an item so that inline webform errors will be
      // displayed.
      $row['likert_question'] = [
        '#type' => 'item',
        '#title' => $question_title,
        // Must include an empty <span> so that the item's value is
        // not required.
        '#value' => '<span></span>',
        '#required' => $element['#required'],
      ];
      if ($question_description_property_name) {
        $row['likert_question'][$question_description_property_name] = $question_description;
      }

      foreach ($answers as $answer_key => $answer) {

        $row[$answer_key] = [
          '#parents' => [$element['#name'], $question_key],
          '#type' => 'radio',
          // Must cast values as strings to prevent NULL and empty strings.
          // from being evaluated as 0.
          '#return_value' => (string) $answer_key,
          '#value' => (string) $value,
        ];

        // Wrap title in span.webform-likert-label so that it can hidden when
        // Likert is displayed in grid on desktop.
        // @see webform.element.likert.css
        $row[$answer_key]['#title_display'] = 'after';

        switch ($answer['description_property_name']) {
          case 'help':
            $build = [
              'title' => ['#markup' => $answer['title']],
              'help' => [
                '#type' => 'webform_help',
                '#help' => $answer['description'],
              ],
              '#prefix' => '<span class="webform-likert-label">',
              '#suffix' => '</span>',
            ];
            $row[$answer_key]['#title'] = \Drupal::service('renderer')->render($build);
            break;

          case 'description':
            $row[$answer_key] += [
              '#title' => new FormattableMarkup('<span class="webform-likert-label">@title</span>', ['@title' => $answer['title']]),
              '#description' => new FormattableMarkup('<span class="webform-likert-description">@description</span>', ['@description' => $answer['description']]),
            ];
            break;

          default:
            $row[$answer_key] += [
              '#title' => new FormattableMarkup('<span class="webform-likert-label">@title</span>', ['@title' => $answer['title']]),
            ];
        }
      }
      $rows[$question_key] = $row;
    }

    $element['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#attributes' => [
        'class' => ['webform-likert-table'],
        'data-likert-answers-count' => count($element['#answers']),
      ],
    ] + $rows;

    // Build table element with selected properties.
    $properties = [
      '#states',
      '#sticky',
    ];
    $element['table'] += array_intersect_key($element, array_combine($properties, $properties));

    $element['#tree'] = TRUE;

    // Add validate callback.
    $element += ['#element_validate' => []];
    array_unshift($element['#element_validate'], [get_called_class(), 'validateWebformLikert']);

    $element['#attached']['library'][] = 'webform/webform.element.likert';

    return $element;
  }

  /**
   * Get likert element's answer which can include an N/A option.
   *
   * @param array $element
   *   The element.
   */
  public static function processWebformLikertAnswers(array &$element) {
    if (empty($element['#na_answer']) || empty($element['#answers'])) {
      return;
    }

    $na_value = (!empty($element['#na_answer_value'])) ? $element['#na_answer_value'] : (string) t('N/A');
    $na_text = (!empty($element['#na_answer_text'])) ? $element['#na_answer_text'] : $na_value;
    $element['#answers'] += [
      $na_value => $na_text,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $default_value = [];
    foreach ($element['#questions'] as $question_key => $question_title) {
      $default_value[$question_key] = NULL;
    }

    if ($input === FALSE) {
      $element += ['#default_value' => []];
      return $element['#default_value'] + $default_value;
    }
    $value = $default_value;
    foreach ($value as $allowed_key => $default) {
      if (isset($input[$allowed_key]) && is_scalar($input[$allowed_key])) {
        $value[$allowed_key] = (string) $input[$allowed_key];
      }
    }
    return $value;
  }

  /**
   * Validates a likert element.
   */
  public static function validateWebformLikert(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = $element['#value'];

    if (!empty($element['#required'])) {
      foreach ($element['#questions'] as $question_key => $question_title) {
        if (empty($value[$question_key])) {
          $form_state->setError($element['table'][$question_key]['likert_question'], t('@name field is required.', ['@name' => $question_title]));
        }
      }
    }

    $element['#value'] = $value;
    $form_state->setValueForElement($element, $value);
  }

}
