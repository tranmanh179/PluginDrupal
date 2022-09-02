<?php

namespace Drupal\user_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller routines for help routes.
 */
class UserDashboardController extends ControllerBase {

  /**
   * Redirect to user dashboard.
   *
   * @inheritDoc
   */
  public function goto() {
    return $this->redirect('user_dashboard.id_dashboard', [
      'user' => \Drupal::currentUser()
        ->id(),
    ]);
  }

  /**
   * Page user dashboard.
   *
   * @inheritDoc
   */
  public function dashboard($user) {
    $js_settings = [
      'dashboard' => [
        'drawer' => Url::fromRoute('user_dashboard.drawer', ['user' => $user->id()])
          ->toString(),
        'blockContent' => Url::fromRoute('user_dashboard.block_content', ['user' => $user->id()])
          ->toString(),
        'updatePath' => Url::fromRoute('user_dashboard.update', ['user' => $user->id()])
          ->toString(),
        'customize' => Url::fromRoute('user_dashboard.customize', ['user' => $user->id()])
          ->toString(),
        'dashboard' => Url::fromRoute('user_dashboard.id_dashboard', ['user' => $user->id()])
          ->toString(),
        'emptyBlockText' => t('empty'),
        'emptyRegionTextInactive' => t('This dashboard region is empty. Click <em>Customize dashboard</em> to add blocks to it.'),
        'emptyRegionTextActive' => t('DRAG HERE'),
      ],
    ];
    $regions = \Drupal::service('user_dashboard.blocks')
      ->getUserdashboardRegionsBlocks($user);
    $build = [
      '#theme' => 'user_dashboard_page',
      '#regions' => $regions,
      '#message' =>
      t('To customize the dashboard page, move blocks to the dashboard regions on the <a href="@dashboard">Dashboard administration page</a>, or enable JavaScript on this page to use the drag-and-drop interface.', [
        '@dashboard' => Url::fromRoute('user_dashboard.set_default')
          ->toString(),
      ]),
      '#configuration' =>
      t('Drag and drop these blocks to the columns below. Changes are automatically saved. More options are available on the UserDashboard <a href="@dashboard">configuration page</a>.', [
        '@dashboard' => Url::fromRoute('user_dashboard.settings')
          ->toString(),
      ]),
      '#customize' => t('Customize dashboard'),
      '#attached' => [
        'library' => ['user_dashboard/user_dashboard'],
        'drupalSettings' => $js_settings,
      ],
      '#cache' => ['max-age' => 0],
    ];

    return $build;
  }

  /**
   * Customize.
   *
   * @inheritDoc
   */
  public function customize($user) {
    $blocks = $this->getBlocksAvailable($user);
    // Remove blocks selected.
    $blockSelected = \Drupal::service('user_dashboard.blocks')
      ->getUserdashboardBlocks($user);
    foreach ($blocks as $delta => $block) {
      if (!empty($blockSelected[$delta])) {
        unset($blocks[$delta]);
      }
    }
    $build = [
      '#theme' => 'user_dashboard_disabled_blocks',
      '#blocks' => $blocks,
    ];
    return new Response(\Drupal::service('renderer')->render($build));
  }

  /**
   * Drawer.
   *
   * @inheritDoc
   */
  public function drawer($user) {
    $uid = is_numeric($user) ? $user : $user->id();
    $region = \Drupal::request()->request->get('region');
    $blockId = \Drupal::request()->request->get('blockId');
    $query = \Drupal::database()
      ->select('user_dashboard_block', 'b')
      ->fields('b', ['bid'])
      ->condition('delta', $blockId)
      ->condition('uid', $uid);
    $bid = $query->execute()->fetchField();
    if (empty($bid)) {
      $bid = \Drupal::database()->insert('user_dashboard_block')
        ->fields(['delta', 'status', 'weight', 'region', 'uid'])
        ->values([$blockId, 1, 0, $region, $uid])
        ->execute();
      \Drupal::service('cache.render')->invalidateAll();
    }

    return new JsonResponse([
      'data' => ['id' => $bid],
      'method' => 'GET',
      'status' => 200,
    ]);
  }

  /**
   * Update status user dashboard.
   *
   * @inheritDoc
   */
  public function update($user) {
    $uid = is_numeric($user) ? $user : $user->id();
    $bid = \Drupal::request()->request->get('bid');
    $action = \Drupal::request()->request->get('action');
    $result = FALSE;
    if ($action == 'delete') {
      $query = \Drupal::database()->delete('user_dashboard_block');
      $query->condition('bid', $bid);
      $query->condition('uid', $uid);
      $result = $query->execute();
    }
    if ($action == 'collapse') {
      $status = \Drupal::request()->request->get('status');
      $status = empty($status) ? 2 : 0;
      $query = \Drupal::request()->update('user_dashboard_block')
        ->fields(['custom' => $status]);
      $query->condition('bid', $bid);
      $query->condition('uid', $uid);
      $result = $query->execute();
    }
    return new JsonResponse([
      'data' => ['result' => $result],
      'method' => 'GET',
      'status' => 200,
    ]);
  }

  /**
   * Show block content.
   *
   * @inheritDoc
   */
  public function showBlockContent($user, $delta = '') {
    $build = \Drupal::service('user_dashboard.blocks')
      ->renderUserdashboardBlock($user, $delta);
    return new Response(\Drupal::service('renderer')->render($build));
  }

  /**
   * Get all of blocks available.
   *
   * @inheritDoc
   */
  private function getBlocksAvailable($user) {
    $blocksAvailable = $this->config('user_dashboard.settings')
      ->get('user_dashboard_available_blocks');
    $definitions = \Drupal::service('user_dashboard.blocks')->getDefinitions();
    if (empty($blocksAvailable)) {
      return $definitions;
    }
    else {
      foreach ($blocksAvailable as $index => $available) {
        $blocksAvailable[$index] = $definitions[$available];
      }
    }
    return $blocksAvailable;
  }

}
