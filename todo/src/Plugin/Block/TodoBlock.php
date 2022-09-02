<?php

namespace Drupal\todo\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'TodoBlock' block.
 *
 * @Block(
 *  id = "todo_block",
 *  admin_label = @Translation("Todo block"),
 * )
 */

class TodoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'todo_block',
      '#content' => 'TEST TODO',
      '#attached' => [
        'library' => ['todo/todo'],
      ],
      '#attributes' => [
        'id' => 'apptodo',
        'class' => [
          'container-fluid',
        ],
      ],
    ];

    return $build;
  }

}
