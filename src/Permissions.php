<?php

namespace Drupal\block_access;

use Drupal\block_content\BlockContentTypeInterface;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a class for generating block content permissions per type.
 */
class Permissions {

  use StringTranslationTrait;

  /**
   * Gets permissions.
   *
   * @return array
   *   Array of permissions as per permissions.yml format.
   */
  public function get() {
    $permissions = [];
    // Generate permissions for all block types types.
    foreach (BlockContentType::loadMultiple() as $type) {
      $permissions += $this->buildPermissions($type);
    }

    return $permissions;
  }

  /**
   * Returns a list of block content permissions for a given type.
   *
   * @param \Drupal\block_content\BlockContentTypeInterface $type
   *   The block content type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(BlockContentTypeInterface $type) {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];

    return [
      "create $type_id block_content" => [
        'title' => $this->t('%type_name: Create new block content', $type_params),
      ],
      "update own $type_id block_content" => [
        'title' => $this->t('%type_name: Edit own block content', $type_params),
      ],
      "update any $type_id block_content" => [
        'title' => $this->t('%type_name: Edit any block content', $type_params),
      ],
      "delete own $type_id block_content" => [
        'title' => $this->t('%type_name: Delete own block content', $type_params),
      ],
      "delete any $type_id block_content" => [
        'title' => $this->t('%type_name: Delete any block content', $type_params),
      ],
    ];
  }

}
