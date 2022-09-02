<?php

namespace Drupal\json_table\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'json_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "json_tables_formatter",
 *   label = @Translation("Json tables"),
 *   field_types = {
 *     "json",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class JsonTablesFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'header' => '',
        'mode' => 'table',
        'first_row' => TRUE,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['mode'] = [
      '#type' => 'select',
      '#options' => [
        'table' => $this->t('Table'),
        'datatables' => $this->t('Datatables'),
        'bootstrap-table' => $this->t('Bootstrap table'),
      ],
      '#default_value' => $this->getSetting('mode'),
    ];
    $form['first_row'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Using first row as a header'),
      '#default_value' => $this->getSetting('first_row'),
    ];
    $form['header'] = [
      '#title' => $this->t('Header'),
      '#type' => 'textarea',
      '#default_value' => $this->getSetting('header'),
      '#description' => $this->t('Separated par ,'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary['mode'] = "Mode: " . $this->getSetting('mode');
    if (!empty($this->getSetting('header'))) {
      $summary['header'] = "Header: " . $this->getSetting('header');
    }
    return $summary;
  }

  /**
   * {@inheritDoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $field_name = $items->getName();

    $header = str_replace([',', ';', "\r\n", "\r"], "\n", $this->getSetting('header'));
    if (!empty($header)) {
      $header = explode("\n", $header);
    }
    $drupalSettings = [
      'json_table' => [$field_name => $this->getSettings()],
    ];
    $elements = [];
    if (!empty($items)) {
      switch ($this->getSetting('mode')) {
        case 'datatables':
          $elements['#attached'] = [
            'library' => ['json_table/json_datatables'],
            'drupalSettings' => $drupalSettings,
          ];
          break;

        case 'bootstrap-table':
          $elements['#attached'] = [
            'library' => ['json_table/json_bootstraptable'],
            'drupalSettings' => $drupalSettings,
          ];
          $data_option = [
            'toggle' => 'table',
            'search' => "true",
            'show-search-clear-button' => "true",
            'show-refresh' => "true",
            'show-toggle' => "true",
            'show-fullscreen' => "true",
            'show-columns' => "true",
            'show-columns-toggle-all' => "true",
            'show-export' => "true",
            'sortable' => "true",
            'click-to-select' => "true",
            'minimum-count-columns' => "2",
            'show-pagination-switch' => "true",
            'pagination' => "true",
            'page-list' => "[10, 25, 50, 100, all]",
            'show-footer' => "false",
          ];
          break;

        default:
          $elements = [];
      }
    }

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'table',
        '#header' => $header ?? [],
        '#rows' => json_decode($item->value),
        '#langcode' => $langcode,
        '#attributes' => [
          'data-json-field' => $field_name,
          'data-delta' => $delta,
          'class' => ['json-table', $this->getSetting('mode')],
          'id' => Html::getUniqueId($field_name . '-' . $delta),
        ],
      ];
      if (!empty($data_option)) {
        foreach ($data_option as $dataTable => $valueData) {
          $elements[$delta]['#attributes']["data-$dataTable"] = $valueData;
        }
      }
    }
    return $elements;
  }

}
