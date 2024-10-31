<?php

namespace Drupal\Tests\readmore_extrafield\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests the readmore_extrafield javascript functionalities.
 *
 * @group readmore_extrafield
 */
class ReadMoreExtraFieldFunctionalJavascriptTest extends WebDriverTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'field',
    'field_ui',
    'test_page_test',
    'extra_field',
    'extra_field_plus',
    'readmore_extrafield',
    'layout_discovery',
    'layout_builder',
  ];

  /**
   * A user with admin permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * A user with authenticated permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * A node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;


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
    $this->node = $this->drupalCreateNode([
      'title' => 'VerySpecificTestTitle',
      'type' => 'article',
      'body' => 'Body field value.',
    ]);

    $this->config('system.site')->set('page.front', '/test-page')->save();

    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test setting apply if setted via ui.
   */
  public function testSetFieldSettingsViaUi() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_readmore_extrafield', [
        'type' => 'extra_field_readmore_extrafield',
        'settings' => [
          'link_title' => 'Link Title Test',
          'link_classes' => 'test-link-class',
          'link_attr_title' => 'Link Attr Title',
          'link_attr_rel' => 'alternate',
          'link_attr_target' => '_self',
        ],
      ])
      ->enable()
      ->save();
    $this->drupalGet('/admin/structure/types/manage/article/display');
    $this->assertNotNull($session->waitForElementVisible('css', '#edit-fields-extra-field-readmore-extrafield-settings-edit'));
    $page->pressButton('edit-fields-extra-field-readmore-extrafield-settings-edit');
    $this->assertNotNull($session->waitForElementVisible('css', 'input[id*="edit-fields-extra-field-readmore-extrafield-settings-edit-form"]'));
    $this->submitForm([
      'fields[extra_field_readmore_extrafield][settings_edit_form][settings][link_title]' => 'Link Title Test Overwritten',
      'fields[extra_field_readmore_extrafield][settings_edit_form][settings][link_classes]' => 'test-link-class-overwritten',
      'fields[extra_field_readmore_extrafield][settings_edit_form][settings][link_attr_title]' => 'Link Attr Title Overwritten',
      'fields[extra_field_readmore_extrafield][settings_edit_form][settings][link_attr_rel]' => 'author',
      'fields[extra_field_readmore_extrafield][settings_edit_form][settings][link_attr_target]' => '_top',
    ], 'Update');
    $page->pressButton('edit-submit');
    $this->drupalGet('/node/' . $this->node->id());

    // Check all settings are set on the Link:
    $session->elementExists('css', 'a.test-link-class-overwritten.readmore-extrafield-link');
    $session->elementExists('css', 'div.readmore-extrafield > a.test-link-class-overwritten.readmore-extrafield-link');
    $session->elementTextEquals('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'Link Title Test Overwritten');
    $session->elementAttributeContains('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'title', 'Link Attr Title Overwritten');
    $session->elementAttributeContains('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'rel', 'author');
    $session->elementAttributeContains('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'target', '_top');
  }

  /**
   * Tests the field settings with all options empty.
   */
  public function testFieldOptionsEmptyFallback() {
    $session = $this->assertSession();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_readmore_extrafield', [
        'type' => 'extra_field_readmore_extrafield',
        'settings' => [
          'link_title' => '',
          'link_classes' => '',
          'link_attr_title' => '',
          'link_attr_rel' => '',
          'link_attr_target' => '',
        ],
      ])
      ->enable()
      ->save();

    $this->drupalGet('/node/' . $this->node->id());

    // Check all settings are (not) set on the Link:
    $session->elementExists('css', 'a.readmore-extrafield-link');
    $session->elementExists('css', 'div.readmore-extrafield > a.readmore-extrafield-link');
    $session->elementTextEquals('css', 'a.readmore-extrafield-link', 'Read more');
    $session->elementAttributeNotExists('css', 'a.readmore-extrafield-link', 'title');
    $session->elementAttributeNotExists('css', 'a.readmore-extrafield-link', 'rel');
    $session->elementAttributeNotExists('css', 'a.readmore-extrafield-link', 'target');
  }

  /**
   * Tests to see if the field behaves correctly inside layout builder.
   */
  public function testFieldInLayoutBuilder() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();

    // Enable Layout Builder:
    $this->drupalGet('/admin/structure/types/manage/article/display');
    $page->checkField('edit-layout-enabled');
    $page->pressButton('edit-submit');

    $this->drupalGet('/layout_builder/choose/block/defaults/node.article.default/0/content');
    $session->pageTextContains('Read more');
    $this->drupalGet('/layout_builder/add/block/defaults/node.article.default/0/content/extra_field_block%3Anode%3Aarticle%3Aextra_field_readmore_extrafield');

    // Test readmore_extrafield settings exist in Layout Builder:
    $session->elementExists('css', '#edit-settings-label');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-link-title');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-link-classes');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-link-attr-title');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-link-attr-rel');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-link-attr-target');

    // Fill these settings:
    $page->fillField('edit-settings-label', 'Test-Label');
    $page->fillField('edit-settings-third-party-settings-extra-field-plus-settings-link-title', 'Link Title Test Overwritten');
    $page->fillField('edit-settings-third-party-settings-extra-field-plus-settings-link-classes', 'test-link-class-overwritten');
    $page->fillField('edit-settings-third-party-settings-extra-field-plus-settings-link-attr-title', 'Link Attr Title Overwritten');
    $page->fillField('edit-settings-third-party-settings-extra-field-plus-settings-link-attr-rel', 'author');
    $page->fillField('edit-settings-third-party-settings-extra-field-plus-settings-link-attr-target', '_top');
    // Save field:
    $page->pressButton('edit-actions-submit');
    // Save layout:
    $page->pressButton('edit-submit');

    // Go to node:
    $this->drupalGet('/node/' . $this->node->id());
    // Check all settings are set on the Link:
    $session->elementExists('css', 'a.test-link-class-overwritten.readmore-extrafield-link');
    $session->elementExists('css', 'div.readmore-extrafield > a.test-link-class-overwritten.readmore-extrafield-link');
    $session->elementTextEquals('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'Link Title Test Overwritten');
    $session->elementAttributeContains('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'title', 'Link Attr Title Overwritten');
    $session->elementAttributeContains('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'rel', 'author');
    $session->elementAttributeContains('css', 'a.test-link-class-overwritten.readmore-extrafield-link', 'target', '_top');
  }

}
