<?php

namespace Drupal\user_dashboard;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Plugin\Context\LazyContextRepository;
use Drupal\Core\Plugin\PluginFormFactoryInterface;
use Drupal\user\Entity\User;

/**
 * User dashboard Service.
 */
class UserDashboardBlocks {

  /**
   * The block manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * The context repository.
   *
   * @var \Drupal\Core\Plugin\Context\LazyContextRepository
   */
  protected $contextRepository;

  /**
   * The plugin form manager.
   *
   * @var \Drupal\Core\Plugin\PluginFormFactoryInterface
   */
  protected $pluginFormFactory;

  /**
   * DraggableDashboardController constructor.
   *
   * @inheritDoc
   */
  public function __construct(BlockManagerInterface $block_manager, LazyContextRepository $context_repository, PluginFormFactoryInterface $plugin_form_manager) {
    $this->blockManager = $block_manager;
    $this->contextRepository = $context_repository;
    $this->pluginFormFactory = $plugin_form_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block'),
      $container->get('context.repository'),
      $container->get('plugin_form.factory')
    );
  }

  /**
   * Get blocks definition.
   *
   * @inheritDoc
   */
  public function getDefinitions() {
    // Only add blocks which work without any available context.
    $definitions = $this->blockManager->getFilteredDefinitions('block_ui', $this->contextRepository->getAvailableContexts());

    // Order by category, and then by admin label.
    $definitions = $this->blockManager->getSortedDefinitions($definitions);
    // Filter out definitions that are not intended to be placed by the UI.
    $definitions = array_filter($definitions, function (array $definition) {
      return empty($definition['_block_ui_hidden']);
    });

    return $definitions;
  }

  /**
   * Shows a list of blocks that can be added to a theme's layout.
   *
   * @inheritDoc
   */
  public function listBlocks() {
    $definitions = $this->getDefinitions();
    $blocks = [];
    foreach ($definitions as $plugin_id => $plugin_definition) {
      $blocks[$plugin_id] = $plugin_definition['admin_label'] . ' - ' . $plugin_definition['category'];
    }
    asort($blocks);
    return $blocks;
  }

  /**
   * Get Regions.
   *
   * @inheritDoc
   */
  public function getRegions() {
    return [
      'main' => [
        'user_dashboard_main' => [
          'name' => 'Dashboard (main)',
          'width' => 8,
        ],
        'user_dashboard_sidebar' => [
          'name' => 'Dashboard (sidebar)',
          'width' => 4,
        ],
      ],
      'second' => [
        'user_dashboard_column1' => [
          'name' => 'Dashboard (column1)',
          'width' => 8,
        ],
        'user_dashboard_column2' => [
          'name' => 'Dashboard (column2)',
          'width' => 4,
        ],
      ],
      'simple' => [
        'user_dashboard_column3' => [
          'name' => 'Dashboard (column3)',
          'width' => 12,
        ],
      ],
      'footer' => [
        'user_dashboard_footer' => [
          'name' => 'Dashboard (footer)',
          'width' => 12,
        ],
      ],
    ];
  }

  /**
   * Get all user blocks shown in dashboard.
   *
   * @inheritDoc
   */
  public function getUserdashboardBlocks($user) {
    $uid = is_numeric($user) ? $user : $user->id();
    $query = \Drupal::database()->select('user_dashboard_block', 'b');
    $query->fields('b')->condition('uid', $uid)
      ->orderBy('weight', 'ASC');
    ;
    return $query->execute()->fetchAllAssoc('delta', \PDO::FETCH_ASSOC);
  }

  /**
   * Get user block in region.
   *
   * @inheritDoc
   */
  public function getUserdashboardRegionsBlocks($user) {
    $regionsBlock = [];
    $blocks = $this->getUserdashboardBlocks($user);
    if (!empty($blocks)) {
      foreach ($blocks as $block) {
        $regionsBlock[$block['region']][$block['delta']] = $this->renderUserdashboardBlock($user, $block['delta']);
        if ($block['custom'] == 2) {
          $regionsBlock[$block['region']][$block['delta']]['#collapse'] = TRUE;
        }
        $regionsBlock[$block['region']][$block['delta']]['#bid'] = $block['bid'];
        $regionsBlock[$block['region']][$block['delta']]['#weight'] = $block['weight'];
      }
    }
    $regions = $this->getRegions();
    foreach ($regions as $index => $row) {
      foreach ($row as $name => $region) {
        if (!empty($regionsBlock[$name])) {
          $regions[$index][$name]['block'] = $regionsBlock[$name];
        }
      }
    }
    return $regions;
  }

  /**
   * Render userdashboard block.
   *
   * @inheritDoc
   */
  public function renderUserdashboardBlock($user, $delta) {
    if (is_numeric($user)) {
      $user = User::load($user);
    }
    $blocks = $this->getDefinitions();
    $config = [];
    if (!empty($blocks[$delta])) {
      $config = ['label' => $blocks[$delta]['admin_label']];
    }
    $plugin_block = $this->blockManager->createInstance($delta, $config);
    $render = '';
    if ($plugin_block->access($user)) {
      $render = $plugin_block->build();
    }
    return [
      '#theme' => 'user_dashboard',
      '#id' => str_replace(':', '-', $delta),
      '#delta' => $delta,
      '#collapse' => FALSE,
      '#title' => $blocks[$delta]['admin_label'],
      '#content' => $render,
      '#weight' => 0,
      '#cach' => ['max-age' => 0],
    ];
  }

}
