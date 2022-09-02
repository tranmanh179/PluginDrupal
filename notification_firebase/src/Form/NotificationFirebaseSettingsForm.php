<?php

namespace Drupal\notification_firebase\Form;

// nạp thư viện
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

// khởi tạo class
class NotificationFirebaseSettingsForm extends ConfigFormBase {

  public static $configName = 'notification_firebase.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  // lấy id form
  public function getFormId() {
    return 'notification_firebase_settings_form';
  }

  // tạo form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::$configName);

    $form['firebase_server_key'] = [
      '#type' => 'textarea',
      '#title' => t('Firebase Server Key'),
      '#description' => t('Your apps: Project Settings -> Cloud Messaging -> Server key'),
      '#default_value' => $config->get('firebase_server_key'),
      '#required' => TRUE,
    ];

    $form['firebase_apiKey_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase API Key'),
      '#description' => t('Your apps: Project Settings -> General -> Web API Key'),
      '#default_value' => $config->get('firebase_apiKey_id'),
    ];

    $form['firebase_project_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase project id'),
      '#description' => t('Google Firebase: Project Settings -> Setting -> Project ID'),
      '#default_value' => $config->get('firebase_project_id'),
      '#required' => TRUE,
    ];

    $form['firebase_sender_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase sender id'),
      '#description' => t('Sender id: Project Settings -> Cloud Messaging -> Project credentials -> Sender ID'),
      '#default_value' => $config->get('firebase_sender_id'),
      '#required' => TRUE,
    ];

    $form['firebase_app_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase app Key'),
      '#description' => t('Your apps: Project Settings -> General -> Your apps -> App ID'),
      '#default_value' => $config->get('firebase_app_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  // xử lý dữ liệu được gửi lên
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(self::$configName)
      ->set('firebase_server_key', $form_state->getValue('firebase_server_key'))
      ->set('firebase_apiKey_id', $form_state->getValue('firebase_apiKey_id'))
      ->set('firebase_project_id', $form_state->getValue('firebase_project_id'))
      ->set('firebase_sender_id', $form_state->getValue('firebase_sender_id'))
      ->set('firebase_app_id', $form_state->getValue('firebase_app_id'))
      ->save();
    return parent::submitForm($form, $form_state);
  }

}
