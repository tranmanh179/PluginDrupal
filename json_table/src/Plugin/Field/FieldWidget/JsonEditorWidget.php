<?php
namespace Drupal\json_table\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A widget bar.
 *
 * @FieldWidget(
 *   id = "json_editor_widget",
 *   label = @Translation("Json table widget"),
 *   field_types = {
 *     "json",
 *     "string"
 *   }
 * )
 */

class JsonEditorWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $elements['value'] = [
      '#title' => t('JSON'),
      '#type' => 'textarea',
      '#default_value' => $items[$delta]->value ?? NULL,
      '#attributes' => [
        'data-json-editor' => $this->getSetting('mode'),
        'class' => ['json-editor' , $this->getSetting('mode')],
      ],
      '#attached' => [
        'library' => ['json_table/json_editor'],
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
  public static function defaultSettings() {
    return [
        // Create the custom setting 'size', and
        // assign a default value of 60
        'mode' => 'code',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Mode editor of textfield'),
      '#default_value' => $this->getSetting('mode'),
      '#options' => [
        'tree' => $this->t('Tree'),
        'code' => $this->t('Code'),
        'text' => $this->t('Text'),
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('Textfield mode: @mode', array('@mode' => $this->getSetting('mode')));

    return $summary;
  }

}
