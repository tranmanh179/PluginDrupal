<?php

namespace Drupal\get_content_url\Form;

// nạp thư viện
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

// khởi tạo class
class GetContentForm extends ConfigFormBase {

  public static $configName = 'get_content_url.get_content';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  // lấy id form
  public function getFormId() {
    return 'get_content_url_get_content';
  }

  // tạo form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['url'] = [
      '#type' => 'textfield',
      '#title' => t('URL content'),
      '#description' => t('Example: https://manmoweb.com'),
      '#default_value' => '',
      '#required' => TRUE,
      '#id' => 'urlGetContent'
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Get content'),
    ];

    return $form;
  }

  // xử lý dữ liệu được gửi lên
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $url= $form_state->getValue('url');
    $output1 = exec('sudo mercury-parser '.$url);
    $output2 = exec('whoami');

    //dpm($url);
    dmp('sudo mercury-parser '.$url);
    var_dump($output1);
    var_dump($output2);
    die;
    return $form;
  }

}
