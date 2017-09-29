<?php

/**
 * @file
 * Contains \Drupal\dbo_blog\Tests\DBOBlogControllerTest.
 */

namespace Drupal\dbo_blog\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the dbo_blog module.
 */
class DBOBlogControllerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "dbo_blog DBOBlogController's controller functionality",
      'description' => 'Test Unit for module dbo_blog and controller DBOBlogController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests dbo_blog functionality.
   */
//  public function testDBOMiddlewareTaxonomiesController() {
  public function testDBOBlogController() {
    // Check that the basic functions of module dbo_middleware_taxonomies.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
