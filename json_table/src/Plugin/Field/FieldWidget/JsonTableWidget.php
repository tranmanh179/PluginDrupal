<?php

namespace Drupal\json_table\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the 'json_editor_widget' widget.
 *
 * @FieldWidget(
 *   id = "json_table_widget",
 *   label = @Translation("Json table"),
 *   field_types = {
 *     "json",
 *   }
 * )
 */
class JsonTableWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'header' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritDoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $textPaste = $this->t('Paste your Excel data here');
    $field_name = $items->getName();
    $id = Html::getUniqueId($field_name . '-' . $delta);
    $header = [];
    $checkJson = json_decode($this->getSetting('header'), TRUE);
    if ($checkJson === FALSE) {
      $header = str_replace([',', ';', "\r\n", "\r"], "\n", $this->getSetting('header'));
      $header = explode("\n", $header);
    }
    else {
      $header = $checkJson;
    }
    $container = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'json-table-wrapper',
        ],
      ],
      'btn' => [
        '#markup' => Markup::create('<button type="button" class="btn btn-link add-row">Add row</button> <button type="button" class="btn btn-link add-col">Add column</button>'),
      ],
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => [
          [Markup::create('<input class="paste form-control" placeholder="' . $textPaste . '"/>'),
          ],
        ],
        '#attributes' => [
          'id' => $id,
          'class' => [
            'json-table',
          ],
        ],
      ],
    ];
    $elements['value'] = [
      '#title' => t('JSON'),
      '#type' => 'textarea',
      '#default_value' => $items[$delta]->value ?? NULL,
      '#description' => Markup::create(\Drupal::service('renderer')->render($container)),
      '#attributes' => [
        'data-json-table' => $this->getSetting('mode'),
        'class' => ['json-table', 'js-hide' , $this->getSetting('mode')],
      ],
      '#attached' => [
        'library' => ['json_table/json_table'],
        'drupalSettings' => [
          'json_editor' => [$delta => $this->getSetting('mode')],
        ],
      ],
      '#element_validate' => [
        [$this, 'validateJsonData'],
      ],
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['header'] = [
      '#type' => 'textarea',
      '#title' => t('Defined header'),
      '#description' => t('You can set the header with json object, for example:') .
        json_encode(['name' => "Col Name", 'age' => 'Col Age']),
      '#default_value' => $this->getSetting('header'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if (!empty($this->getSetting('header'))) {
      $summary[] = t('Custom header');
    }
    return $summary;
  }

  /**
   * Check the submitted JSON against the configured JSON Schema.
   *
   * {@inheritdoc}
   */
  public static function validateJsonData($element, FormStateInterface $form_state) {
    json_decode($element['#value']);
    return json_last_error() == JSON_ERROR_NONE;
  }

}
