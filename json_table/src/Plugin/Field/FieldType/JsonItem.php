<?php

namespace Drupal\json_table\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type of baz.
 *
 * @FieldType(
 *   id = "json",
 *   label = @Translation("Json table field"),
 *   default_formatter = "json_formatter",
 *   default_widget = "json_editor_widget",
 * )
 */

class JsonItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      // columns contains the values that the field will store
      'columns' => [
        // List the values that the field will save. This
        // field will only save a single value, 'value'
        'value' => [
          'type' => 'json',
          'pgsql_type' => 'json',
          'mysql_type' => 'json',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('JSON value'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
        // Declare a single setting, 'size', with a default
        // value of 'large'
        'size' => 'big',
      ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {

    $element = [];
    // The key of the element should be the setting name
    $element['size'] = [
      '#title' => $this->t('Size'),
      '#type' => 'select',
      '#options' => [
        'normal' => $this->t('Normal'),
        'medium' => $this->t('Medium'),
        'big' => $this->t('Big'),
      ],
      '#default_value' => $this->getSetting('size'),
    ];

    return $element;
  }

}
