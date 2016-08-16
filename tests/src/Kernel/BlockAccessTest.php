<?php

namespace Drupal\Tests\block_access\Kernel;

use Drupal\block_content\Entity\BlockContent;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\simpletest\UserCreationTrait;
use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * Tests block access functionality.
 *
 * @group block_access
 */
class BlockAccessTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * Test block type.
   *
   * @var \Drupal\block_content\BlockContentTypeInterface
   */
  protected $blockType1;

  /**
   * Test block type.
   *
   * @var \Drupal\block_content\BlockContentTypeInterface
   */
  protected $blockType2;

  /**
   * Test block content.
   *
   * @var \Drupal\block_content\BlockContentInterface
   */
  protected $blockContent1;

  /**
   * Test block content.
   *
   * @var \Drupal\block_content\BlockContentInterface
   */
  protected $blockContent2;

  /**
   * Test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user1;

  /**
   * Test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user2;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'block_access',
    'block_content',
    'block',
    'system',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('block_content');
    $this->installEntitySchema('user');

    // Setup some test entities.
    $this->blockType1 = BlockContentType::create([
      'id' => 'type1',
      'label' => 'name1',
    ]);
    $this->blockType1->save();
    $this->blockType2 = BlockContentType::create([
      'id' => 'type2',
      'label' => 'name2',
    ]);
    $this->blockType2->save();

    $this->user1 = $this->createUser([
      'edit any type1 block_content',
      'delete any type1 block_content',
      'create type1 block_content',
      'edit own type2 block_content',
      'delete own type2 block_content',
    ]);

    $this->user2 = $this->createUser([
      'edit any type2 block_content',
      'delete any type2 block_content',
      'create type2 block_content',
      'edit own type1 block_content',
      'delete own type1 block_content',
    ]);

    $this->blockContent1 = BlockContent::create([
      'type' => 'type1',
      'info' => 'block1',
      'revision_user' => $this->user2->id(),
    ]);
    $this->blockContent1->save();
    $this->blockContent2 = BlockContent::create([
      'type' => 'type2',
      'info' => 'block1',
      'revision_user' => $this->user1->id(),
    ]);
    $this->blockContent2->save();
  }

  /**
   * Tests block access.
   */
  public function testAccess() {
    $this->fail();
  }

}
