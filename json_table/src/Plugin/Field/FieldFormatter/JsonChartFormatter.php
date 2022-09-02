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
 *   id = "json_chart_formatter",
 *   label = @Translation("Json chart"),
 *   field_types = {
 *     "json",
 *   },
 * )
 */
class JsonChartFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'mode' => 'googleCharts',
        'chart_type' => 'LineChart',
        'chart_width' => 900,
        'chart_height' => 300,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['mode'] = [
      '#type' => 'select',
      '#options' => [
        'googleCharts' => $this->t('Google chart'),
        'highChart' => $this->t('High chart'),
      ],
      '#default_value' => $this->getSetting('mode'),
    ];
    $form['chart_type'] = [
      '#title' => $this->t('Chart type'),
      '#description' => '<a href="https://developers-dot-devsite-v2-prod.appspot.com/chart/interactive/docs/gallery" target="_blank">Google charts</a>',
      '#type' => 'select',
      '#default_value' => $this->getSetting('chart_type'),
      '#options' => $this->googleChartsOption(),
      '#empty_option' => $this->t('Default'),
      '#states' => [
        'visible' => [
          'select[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][mode]"]' => ['value' => 'googleCharts'],
        ],
      ],
    ];

    $form['chart_width'] = [
      '#title' => $this->t('Chart width'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('chart_width'),
      '#states' => [
        'visible' => [
          'select[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][mode]"]' => ['value' => 'googleCharts'],
        ],
      ],
    ];
    $form['chart_height'] = [
      '#title' => $this->t('Chart height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('chart_height'),
      '#states' => [
        'visible' => [
          'select[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][mode]"]' => ['value' => 'googleCharts'],
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary['mode'] = "Mode: " . $this->getSetting('mode');
    $summary['chart_type'] = "Type: " . $this->getSetting('chart_type');
    return $summary;
  }

  /**
   * {@inheritDoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $field_name = $items->getName();
    $drupalSettings = [
      $this->getSetting('mode') => ['#' . $field_name => $this->getSettings()],
    ];

    $elements = [];
    if (!empty($items)) {
      switch ($this->getSetting('mode')) {
        case 'googleCharts':
          $elements['#attached'] = [
            'library' => ['json_table/googleCharts'],
            'drupalSettings' => $drupalSettings,
          ];
          break;

        case 'highChart':
          $elements['#attached'] = [
            'library' => ['json_table/highcharts'],
            'drupalSettings' => $drupalSettings,
          ];
          break;

        default:
          $elements = [];
      }
    }
    $setting = $this->getSettings();
    $options = $this->googleChartsOption($setting['chart_type']);
    $options['url'] = FALSE;
    if (!empty($setting['caption'])) {
      $options['title'] = $setting['caption'];
    }
    if (is_numeric($setting['chart_width'])) {
      $setting['chart_width'] .= 'px';
    }
    if (is_numeric($setting['chart_height'])) {
      $setting['chart_height'] .= 'px';
    }
    if (empty($setting['chart_width'])) {
      $setting['chart_width'] = '100%';
    }
    foreach ($items as $delta => $item) {
      $id = Html::getUniqueId($field_name . '-' . $delta);
      $elements['#attached']['drupalSettings'][$this->getSetting('mode')]['#' . $id] = [
        'id' => $id,
        'type' => $this->getSetting('chart_type'),
        'options' => $options,
        'data' => json_decode($item->value, true),
      ];
      $elements[$delta] = [
        '#theme' => 'json_table_chart',
        '#settings' => $setting,
        '#id_field_name' => $id,
        '#langcode' => $langcode,
        '#attributes' => [
          'data-json-field' => $field_name,
          'data-delta' => $delta,
          'class' => [$this->getSetting('mode')],
          'id' => $id,
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

  /**
   * {@inheritDoc}
   */
  private function googleChartsOption($option = FALSE) {
    $options = [
      'BarChart' => [
        'title' => $this->t('Bar'),
        'option' => [
          'bar' => ['groupWidth' => "95%"],
          'legend' => ['position' => "none"],
        ],
      ],
      'BubbleChart' => [
        'title' => $this->t('Bubble'),
        'option' => [
          'bubble' => ['textStyle' => ['fontSize' => 11]],
        ],
      ],
      'LineChart' => [
        'title' => $this->t('Line'),
        'option' => [
          'legend' => ['position' => "bottom"],
          'curveType' => 'function',
        ],
      ],
      'ColumnChart' => [
        'title' => $this->t('Column'),
        'option' => [
          'bar' => ['groupWidth' => "95%"],
          'legend' => ['position' => "none"],
        ],
      ],
      'ComboChart' => [
        'title' => $this->t('Combo'),
        'option' => [
          'seriesType' => 'bars',
        ],
      ],
      'PieChart' => [
        'title' => $this->t('Pie'),
        'option' => [
          'is3D' => TRUE,
        ],
      ],
      'ScatterChart' => [
        'title' => $this->t('Scatter'),
        'option' => [
          'legend' => ['position' => "none"],
        ],
      ],
      'SteppedAreaChart' => [
        'title' => $this->t('Stepped Area'),
        'option' => [
          'isStacked' => TRUE,
        ],
      ],
      'AreaChart' => [
        'title' => $this->t('Area'),
        'option' => [
          'legend' => ['position' => "top", 'maxLines' => 3],
          'isStacked' => 'relative',
        ],
      ],
      'Histogram' => [
        'title' => $this->t('Histogram'),
        'option' => [
          'legend' => ['position' => "top", 'maxLines' => 3],
          'interpolateNulls' => FALSE,
        ],
      ],
      'CandlestickChart' => [
        'title' => $this->t('Candlestick'),
        'option' => [
          'notHeader' => TRUE,
          'legend' => 'none',
          'bar' => ['groupWidth' => '100%'],
        ],
      ],
    ];
    if ($option) {
      return $options[$option]['option'];
    }
    $titleOptions = [];
    foreach ($options as $type => $option) {
      $titleOptions[$type] = $option['title'];
    }
    return $titleOptions;
  }
}
