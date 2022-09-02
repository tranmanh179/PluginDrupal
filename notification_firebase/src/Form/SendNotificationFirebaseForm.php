<?php

namespace Drupal\notification_firebase\Form;

// nạp thư viện
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

// khởi tạo class
class SendNotificationFirebaseForm extends ConfigFormBase {

  public static $configName = 'notification_firebase.send_notification';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  // lấy id form
  public function getFormId() {
    return 'notification_firebase_send_form';
  }

  // tạo form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::$configName);

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#description' => '',
      '#default_value' => '',
      '#required' => TRUE,
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => t('URL open'),
      '#description' => t('Example: https://manmoweb.com'),
      '#default_value' => '',
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => t('Message'),
      '#description' => '',
      '#default_value' => '',
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send notification'),
    ];

    return $form;
  }

  // xử lý dữ liệu được gửi lên
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();
    $target = $database->select('notification_firebase', 'f')
      ->fields('f', ['token'])
      ->execute()
      ->fetchCol();
    
    if(!empty($target)){
      $title= $form_state->getValue('title');
      $message= $form_state->getValue('message');
      $url= $form_state->getValue('url');
      $icon= 'https://quanlyluutru.com/app/Plugin/mantanHotel/view/ver3/images/icon.png';

      $number= 0;
      foreach($target as $token){
        \Drupal::service('notification_firebase.sendNotificationToUser')->sendNotificationToUser($token, $title, $message, $url, $icon);
        $number++;
      }

      \Drupal::messenger()
        ->addMessage($this->t("Send successful $number notification"));
    }else{
      \Drupal::messenger()
        ->addMessage($this->t("Token device empty"));
    }
    
    return $form;
  }

}
