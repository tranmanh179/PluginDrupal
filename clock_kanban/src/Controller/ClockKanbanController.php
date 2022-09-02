<?php

namespace Drupal\clock_kanban\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class ClockKanbanController.
 */
class ClockKanbanController extends ControllerBase {
  public function saveStartCountingTime() {
    $idNode = \Drupal::request()->request->get('idNode');

    if(!empty($idNode)){
      $database = \Drupal::database();
      $hasNodeTime = $database->select('clock_kanban', 'f')
        ->fields('f')
        ->condition('idNode', $idNode, '=')
        ->condition('uid', \Drupal::currentUser()->id(), '=')
        ->condition('timeEnd', 0, '=')
        ->execute()
        ->fetchAssoc();

      if (!$hasNodeTime) {
        // dừng tất cả các việc đang làm
        $hasNodeTime = $database->select('clock_kanban', 'f')
          ->fields('f')
          ->condition('uid', \Drupal::currentUser()->id(), '=')
          ->condition('timeEnd', 0, '=')
          ->execute()
          ->fetchAssoc();

        if ($hasNodeTime) {
          $timeCount= time() - $hasNodeTime['timeStart'];

          $database->update('clock_kanban')
            ->fields([
              'timeEnd' => time(),
              'timeCount' => $timeCount
            ])
            ->condition('id', $hasNodeTime['id'], '=')
            ->execute();
        }

        // tất cả các bản ghi đều đã kết thúc
        $database->insert('clock_kanban')
          ->fields([
            'idNode' => $idNode,
            'timeStart' => time(),
            'timeEnd' => 0,
            'timeCount' => 0,
            'uid' => \Drupal::currentUser()->id(),
            'created' => date("Y-m-d H:i:s"),
          ])
          ->execute();

        return array('#markup'=> t('Start counting time first'));
      }
    } else {
      return array('#markup'=> t('Empty id node'));
    }

    return array('#markup'=> t('Error'));
    //return FALSE;
  }

  public function savePauseCountingTime() {
    $idNode = \Drupal::request()->request->get('idNode');

    if(!empty($idNode)){
      $database = \Drupal::database();
      $hasNodeTime = $database->select('clock_kanban', 'f')
        ->fields('f')
        ->condition('idNode', $idNode, '=')
        ->condition('uid', \Drupal::currentUser()->id(), '=')
        ->condition('timeEnd', 0, '=')
        ->execute()
        ->fetchAssoc();

      if ($hasNodeTime) {
        $timeCount= time() - $hasNodeTime['timeStart'];

        $database->update('clock_kanban')
          ->fields([
            'timeEnd' => time(),
            'timeCount' => $timeCount
          ])
          ->condition('id', $hasNodeTime['id'], '=')
          ->execute();

        return array('#markup'=> t('Pause counting time'));
      } else {
        return array('#markup'=> t('Not Have Node Time'));
      }
    } else {
      return array('#markup'=> t('Empty id node'));
    }

    return array('#markup'=> t('Error'));
    //return FALSE;
  }
}
