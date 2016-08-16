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
   * Test block content.
   *
   * @var \Drupal\block_content\BlockContentInterface
   */
  protected $blockContent3;

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
   * Test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user3;

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

    // First user is user 1, has all permissions.
    $this->createUser();

    $this->user1 = $this->createUser([
      'update any type1 block_content',
      'delete any type1 block_content',
      'create type1 block_content',
      'update own type2 block_content',
      'delete own type2 block_content',
    ]);

    $this->user2 = $this->createUser([
      'update any type2 block_content',
      'delete any type2 block_content',
      'create type2 block_content',
      'update own type1 block_content',
      'delete own type1 block_content',
    ]);

    $this->user3 = $this->createUser();

    $this->blockContent1 = BlockContent::create([
      'type' => 'type1',
      'info' => 'block1',
      'revision_user' => $this->user2->id(),
    ]);
    $this->blockContent1->save();
    $this->blockContent2 = BlockContent::create([
      'type' => 'type2',
      'info' => 'block2',
      'revision_user' => $this->user2->id(),
    ]);
    $this->blockContent2->save();
    $this->blockContent3 = BlockContent::create([
      'type' => 'type2',
      'info' => 'block3',
      'revision_user' => $this->user1->id(),
    ]);
    $this->blockContent3->save();
  }

  /**
   * Tests block access.
   */
  public function testAccess() {
    // User has edit-any permission.
    $this->assertTrue($this->blockContent1->access('update', $this->user1));
    // User has edit-own permission.
    $this->assertTrue($this->blockContent1->access('update', $this->user2));
    // User has no permission.
    $this->assertFalse($this->blockContent1->access('update', $this->user3));

    // User has delete-any permission.
    $this->assertTrue($this->blockContent1->access('delete', $this->user1));
    // User has delete-own permission.
    $this->assertTrue($this->blockContent1->access('delete', $this->user2));
    // User has no permission.
    $this->assertFalse($this->blockContent1->access('delete', $this->user3));

    // User has no permission.
    $this->assertFalse($this->blockContent2->access('update', $this->user1));
    // User has edit-any permission.
    $this->assertTrue($this->blockContent2->access('update', $this->user2));
    // User has no permission.
    $this->assertFalse($this->blockContent2->access('update', $this->user3));

    // User has no permission.
    $this->assertFalse($this->blockContent2->access('delete', $this->user1));
    // User has delete-any permission.
    $this->assertTrue($this->blockContent2->access('delete', $this->user2));
    // User has no permission.
    $this->assertFalse($this->blockContent2->access('delete', $this->user3));

    // User has edit-own permission.
    $this->assertTrue($this->blockContent3->access('update', $this->user1));
    // User has edit-any permission.
    $this->assertTrue($this->blockContent3->access('update', $this->user2));
    // User has no permission.
    $this->assertFalse($this->blockContent3->access('update', $this->user3));

    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $this->container->get('entity_type.manager');
    $handler = $entity_type_manager->getAccessControlHandler('block_content');

    // User has create access.
    $this->assertTrue($handler->createAccess('type1', $this->user1));
    $this->assertTrue($handler->createAccess('type2', $this->user2));
    $this->assertFalse($handler->createAccess('type1', $this->user2));
    $this->assertFalse($handler->createAccess('type2', $this->user1));
  }

}
