<?php

namespace Drupal\Tests\extra_field_plus_example\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * This class provides methods specifically for testing something.
 *
 * @group extra_field_plus_example
 */
class ExtraFieldPluginPlusExampleFunctionalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field',
    'field_ui',
    'test_page_test',
    'extra_field',
    'extra_field_plus',
    'extra_field_plus_example',
  ];

  /**
   * A user with authenticated permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * A user with admin permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createContentType(['type' => 'article']);

    $this->config('system.site')->set('page.front', '/test-page')->save();
    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests, if the Extra Field Plugins List has new example entries.
   */
  public function testExtraFieldPluginsListExampleEntries(): void {
    $session = $this->assertSession();
    $this->drupalGet('/admin/reports/extra_fields');
    $session->statusCodeEquals(200);
    // Check if not plugins are registered:
    $session->pageTextContains('Extra Field Plus: Example Node Label');
    $session->pageTextContains('Extra Field Plus: Example Node Label Formatted');
  }

  /**
   * Tests if the example display plugins appear on node manage display.
   */
  public function testFieldPluginExamplesAppearOnManageDisplay(): void {
    $session = $this->assertSession();
    $this->drupalGet('/admin/structure/types/manage/article/display');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Extra Field Plus: Example Node Label');
    $session->pageTextContains('Extra Field Plus: Example Node Label Formatted');
  }

}
