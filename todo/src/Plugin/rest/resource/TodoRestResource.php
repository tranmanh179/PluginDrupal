<?php

namespace Drupal\todo\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "todo_rest_resource",
 *   label = @Translation("Todo rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/vue/todo",
 *     "update" = "/api/vue/todo",
 *     "create" = "/api/vue/todo"
 *   }
 * )
 */

class TodoRestResource extends ResourceBase {
  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('vue_todo'),
      $container->get('current_user'),
      $container->get('state')
    );
  }

  /**
   * Responds to GET requests.
   *
   * {@inheritDoc}
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!\Drupal::currentUser()->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    $dependency = [
      '#cache' => [
        'max-age' => 0,
      ],
    ];
    $data = \Drupal::state()->get('vue-todo');
    return (new ResourceResponse($data))->addCacheableDependency($dependency);
  }

  /**
   * Responds to PUT requests.
   *
   * @param array $data
   *   Given data to store into vue-todo state.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException Throws exception expected.
   */
  public function put(Request $request) {
    \Drupal::state()->set('vue-todo', $data ?? []);
    return new ResourceResponse(['saved' => TRUE]);
  }

  /**
   * Responds to POST requests.
   *
   * @param array $data
   *   Given data to store into vue-todo state.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException Throws exception expected.
   */
  public function post($data = []) {
    \Drupal::state()->set('vue-todo', $data ?? []);
    return new ResourceResponse(['saved' => TRUE]);
  }
}
