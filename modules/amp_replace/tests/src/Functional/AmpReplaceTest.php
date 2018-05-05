<?php

namespace Drupal\Tests\amp\Functional;

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\simpletest\ContentTypeCreationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\amp\Functional\AmpTestBase;

/**
 * Tests AMP view mode.
 *
 * @group amp
 */
class AmpReplaceTest extends AmpTestBase {


  /**
   * Test the AMP view mode.
   */
  public function testAmpViewMode() {

    // Create a node to test AMP page.
    $node = Node::create([
      'type' => 'article',
      'title' => $this->randomMachineName(),
      'body' => 'AMP test body',
    ]);
    $node->save();

    // Check the metadata of the full display mode.
    $node_url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE])->toString();
    $amp_node_url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE, 'query' => ['amp' => NULL]])->toString();

    // Test the warnfix parameter.
    $this->drupalGet($amp_node_url . "&warnfix");
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('AMP test body');
    $this->assertSession()->pageTextContains('AMP-HTML Validation Issues and Fixes');
    $this->assertSession()->pageTextContains('-------------------------------------');
    $this->assertSession()->pageTextContains('PASS');

  }
}
