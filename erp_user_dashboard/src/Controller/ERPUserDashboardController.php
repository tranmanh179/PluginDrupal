<?php

namespace Drupal\erp_user_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class ERPUserDashboardController.
 */
class ERPUserDashboardController extends ControllerBase {

  public function index() {
    $blockManager = \Drupal::service('plugin.manager.block');
    $contextRepository = \Drupal::service('context.repository');

    // Get blocks definition

    $listBlock = $blockManager->getDefinitionsForContexts($contextRepository->getAvailableContexts());

    //dpm($listBlock);
    return array('#theme'=> 'erp_user_dashboard_dashboard',
                  '#listBlock'=> $listBlock
                );
  }

  

}
