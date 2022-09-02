<?php

namespace Drupal\erp_user_dashboard\Form;

// nạp thư viện
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

// khởi tạo class
class ERPUserDashboardForm extends ConfigFormBase {

  public static $configName = 'erp_user_dashboard.dashboard';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  // lấy id form
  public function getFormId() {
    return 'erp_user_dashboard_show';
  }

  // tạo form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $blockManager = \Drupal::service('plugin.manager.block');
    $contextRepository = \Drupal::service('context.repository');

    // Get blocks definition

    $definitions = $blockManager->getDefinitionsForContexts($contextRepository->getAvailableContexts());

    dpm($definitions);
    //dpm($blockManager);

    return $form;
  }

  // xử lý dữ liệu được gửi lên
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    
    return $form;
  }

}
