<?php

namespace Drupal\Tests\amp\Functional;

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\simpletest\ContentTypeCreationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests AMP view mode.
 *
 * @group amp
 */
class AmpFormatterTest extends BrowserTestBase {

  use ContentTypeCreationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'amp',
    'node',
    'contextual',
    'field_ui',
    'quickedit',
  ];

  /**
   * Permissions to grant admin user.
   *
   * @var array
   */
  protected $permissions = [
    'access administration pages',
    'access in-place editing',
    'administer content types',
    'administer display modes',
    'administer node display',
    'administer site configuration',
  ];

  /**
   * An user with administration permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Install the AMP theme.
    \Drupal::service('theme_handler')->install(array('amptheme'));

    // Create Article node type.
    $this->createContentType([
      'type' => 'article',
      'name' => 'Article'
    ]);
  }

  /**
   * Test the AMP view mode.
   */
  public function testAmpViewMode() {

    // Login as an admin user.
    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->drupalLogin($this->adminUser);

    // Create a node to test AMP field formatters.
    $node = Node::create([
      'type' => 'article',
      'title' => $this->randomMachineName(),
      'body' => 'AMP test body',
    ]);
    $node->save();

    // Check that the AMP view mode is available.
    $view_modes_url = Url::fromRoute('entity.entity_view_mode.collection')->toString();
    $this->drupalGet($view_modes_url);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('AMP');

    // Enable AMP display on article content.
    $article_url = Url::fromRoute("entity.entity_view_display.node.default", ['node_type' => 'article'])->toString();
    $this->drupalGet($article_url);
    $this->assertSession()->statusCodeEquals(200);
    $edit = ['display_modes_custom[amp]' => 'amp'];
    $this->submitForm($edit, t('Save'));

    // Check the metadata of the full display mode.
    $node_url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()])->toString();
    $this->drupalGet($node_url);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('AMP test body');
    $this->assertSession()->responseContains('data-quickedit-field-id="node/1/body/en/full"');
    $this->assertSession()->responseContains('link rel="amphtml" href="/node/1?amp"');

    // Check the metadata of the AMP display mode.
    $this->drupalGet('node/1', ['query' => ['amp' => TRUE]]);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('AMP test body');
    $this->assertSession()->responseContains('data-quickedit-field-id="node/1/body/en/amp"');
    $this->assertSession()->responseContains('link rel="canonical" href="/node/1"');
  }
}
