<?php

namespace Drupal\user_dashboard\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

/**
 * Set show block dashboard by role.
 */
class UserDashboardByRole extends ConfigFormBase {

  /**
   * Name of the form.
   *
   * @var string
   */
  public static $formName = 'user_dashboard.role';

  /**
   * Configuration name.
   *
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return [self::$formName];
  }

  /**
   * Form Id.
   *
   * @inheritDoc
   */
  public function getFormId() {
    return 'user_dashboard_role_form';
  }

  /**
   * Build form.
   *
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::$formName);
    $blocks = \Drupal::service('user_dashboard.blocks')->listBlocks();
    $blocksAvailable = $this->config('user_dashboard.settings')->get('user_dashboard_available_blocks');
    if (!empty($blocksAvailable)) {
      foreach ($blocks as $delta => $block) {
        if (empty($blocksAvailable[$delta])) {
          unset($blocks[$delta]);
        }
      }
    }
    $regions = \Drupal::service('user_dashboard.blocks')->getRegions();
    $roles = Role::loadMultiple();
    // Remove role anonymous.
    unset($roles['anonymous']);
    $numRow = [1 => [12], 2 => [8, 4]];

    $form['#attached']['library'] = ['user_dashboard/user_dashboard'];
    foreach ($roles as $role) {
      $roleBlock = 'dashboard-' . $role->id();
      $form[$roleBlock] = [
        '#type' => 'details',
        '#title' => $role->label(),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#id' => 'dashboard',
      ];
      foreach ($regions as $index => $row) {
        $countRow = count($row);
        $i = 0;

        $form[$roleBlock][$index] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['row']],
        ];
        foreach ($row as $id => $region) {
          $col = $numRow[$countRow][$i++];
          $configName = $id . '-' . $role->id();
          $valuesBlock = $config->get($configName);
          $form[$roleBlock][$index][$configName] = [
            '#title' => $region['name'],
            '#type' => 'checkboxes',
            '#multiple' => TRUE,
            '#options' => $blocks,
            '#default_value' => !empty($valuesBlock) ? $valuesBlock : [],
            '#prefix' => '<div class="user-dashboard-checkbox col-' . $col . '">',
            '#suffix' => '</div>',
          ];
        }
      }

    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit form.
   *
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      if (stripos($key, 'user_dashboard') !== 0) {
        continue;
      }
      $value = array_diff($value, [0]);
      if (!empty($value)) {
        $this->config(self::$formName)->set($key, $value)->save();
      }
    }
    parent::submitForm($form, $form_state);
  }

}
