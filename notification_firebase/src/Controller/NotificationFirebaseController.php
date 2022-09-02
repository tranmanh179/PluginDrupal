<?php

namespace Drupal\notification_firebase\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class NotificationFirebaseController.
 */
class NotificationFirebaseController extends ControllerBase {

  public function notification_firebase_settings() {
    return array('#markup'=> t('Hello Manh!'));
  }

  public function saveTokenDevice($token = '') {
    if (empty($token)) {
      $token = $_POST['token'];
    }

    if(!empty($token)){
      $database = \Drupal::database();
      $hasToken = $database->select('notification_firebase', 'f')
        ->fields('f')
        ->condition('token', $token, '=')
        ->execute()
        ->fetchAssoc();

      if (!$hasToken) {
        $database->insert('notification_firebase')
          ->fields([
            'token' => $token,
            'uid' => \Drupal::currentUser()->id(),
            'created' => date("Y-m-d H:i:s"),
          ])
          ->execute();

        return array('#markup'=> t('Đã lưu token'));
        //return json_encode(['uid' => $user->uid]);
      }
    }

    return array('#markup'=> t('Lưu thất bại'));
    //return FALSE;
  }

}
