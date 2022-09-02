<?php
namespace Drupal\todo\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "todo_twig",
 *   admin_label = @Translation("Todo Vue block"),
 *   category = @Translation("Todo")
 * )
 */
class TodoVueBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#markup' => '<div id="apptodo"></div>',
      '#attached' => [
        'library' => ['todo/todo'],
      ],
    ];
    return $build;
  }

}
