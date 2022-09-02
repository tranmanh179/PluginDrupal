<?php

namespace Drupal\user_dashboard\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form user dashboard Settings.
 */
class UserDashboardSettingsForm extends ConfigFormBase {

  /**
   * Name of the config.
   *
   * @var string
   */
  public static $configName = 'user_dashboard.settings';

  /**
   * Configuration name.
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  /**
   * Form Id.
   */
  public function getFormId() {
    return 'user_dashboard_settings_form';
  }

  /**
   * Build Form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::$configName);
    $blocks = \Drupal::service('user_dashboard.blocks')->listBlocks();
    $valuesBlock = $config->get('user_dashboard_available_blocks');
    $form['user_dashboard_available_blocks'] = [
      '#title' => t('Available blocks'),
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#options' => $blocks,
      '#default_value' => !empty($valuesBlock) ? $valuesBlock : [],
      '#description' => t('Choose blocks that can be used on the user dashboard pages.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config(self::$configName)
      ->set('user_dashboard_available_blocks', array_diff($form_state->getValue('user_dashboard_available_blocks'), [0]))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
