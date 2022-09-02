<?php

namespace Drupal\notification_firebase;

/**
 * Service to send notification.
 */
class NotificationFirebase {
    /**
    * Function to send a notification to all the users.
    */
    public function sendNotificationToUser($token='', $title='', $message='', $url='', $icon='')
    {
      $config = \Drupal::config('notification_firebase.settings');

      //FCM api URL
      $urlFirebase = 'https://fcm.googleapis.com/fcm/send';
      
      //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
      $server_key = $config->get('firebase_server_key');
      $data= array( 'title'=>$title,
                    'body'=>$message,
                    'icon'=>$icon,
                    'click_action'=>$url
                  );

      $fields = array();
      $fields['data'] = $data;
      $fields['notification'] = $data;
      $fields['to'] = $token;
      $fields['priority'] = 'high';

      //header with content_type api key
      $headers = array(
          'Content-Type:application/json',
          'Authorization:key='.$server_key
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $urlFirebase);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
      
      dpm($result);
      dpm(json_encode($fields));
      dpm($headers);
      dpm($urlFirebase);
      
      return $result;

    }
}