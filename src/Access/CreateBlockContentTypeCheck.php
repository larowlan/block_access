<?php

namespace Drupal\block_access\Access;

use Drupal\block_content\BlockContentTypeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access control for creation of specific types of block content.
 */
class CreateBlockContentTypeCheck implements AccessInterface {

  /**
   * Determine if a user is allowed to create a specific type of block content.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user attempting to create the content.
   * @param \Drupal\block_content\BlockContentTypeInterface $block_content_type
   *   The type of block content to be created.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Whether the user is allowed to create the content.
   */
  public function access(AccountInterface $account, BlockContentTypeInterface $block_content_type) {
    return AccessResult::allowedIfHasPermissions(
      $account,
      [
        // Default permission to manage all block content types.
        'administer blocks',
        // This is the new per content type permission we have added.
        sprintf('create %s block_content', $block_content_type->id())
      ],
      'OR'
    );
  }
}
