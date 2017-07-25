<?php

/**
 * @file
 * Contains \Drupal\block_access\Routing\RouteSubscriber.
 */

namespace Drupal\block_access\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Replace the default permission check for "administer blocks" with our
    // own which will also support permissions to create individual types of
    // block content.
    if ($route = $collection->get('block_content.add_form')) {
      $requirements = $route->getRequirements();
      unset($requirements['_permission']);
      $requirements['_block_content_access_create'] = 'true';
      $route->setRequirements($requirements);
    }
  }
}
