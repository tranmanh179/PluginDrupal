<?php

namespace Drupal\api_sms\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class APISMSController.
 */
class APISMSController extends ControllerBase {
  public function addSMSApi() {
    $phone = \Drupal::request()->request->get('phone');
    $mess = \Drupal::request()->request->get('mess');

    if(!empty($phone) && !empty($mess)){
      $sms = \Drupal::entityTypeManager()->getStorage('sms_message')->create([
          'type' => 'sms_message',
          'number' => $phone,
          'message' => [
            'value' => $mess,
          ],
          'uid' => \Drupal::currentUser()->id(),
      ]);
      
      $sms->save();

      return array('#markup'=> t('save sms done'));
    }else{
      return array('#markup'=> t('empty data'));
    }
  }
}
