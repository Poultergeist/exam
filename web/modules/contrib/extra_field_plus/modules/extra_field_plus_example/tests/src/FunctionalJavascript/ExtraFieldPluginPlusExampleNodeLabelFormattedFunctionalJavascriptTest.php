<?php

namespace Drupal\Tests\extra_field_plus_example\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests the extra_field_plus_example javascript functionalities.
 *
 * @group extra_field_plus_example
 */
class ExtraFieldPluginPlusExampleNodeLabelFormattedFunctionalJavascriptTest extends WebDriverTestBase {

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
    'extra_field_plus_example',
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
    $driver = $this->getSession()->getDriver();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_example_node_label_formatted', [
        'type' => 'extra_field_example_node_label_formatted',
        'settings' => [
          'link_to_entity' => FALSE,
          'wrapper' => 'span',
        ],
      ])
      ->enable()
      ->save();

    $this->drupalGet('/admin/structure/types/manage/article/display');
    // @todo start from here.
    $page->pressButton('edit-fields-extra-field-example-node-label-formatted-settings-edit');
    $session->waitForElementVisible('css', 'input[id*="edit-fields-extra-field-example-node-label-formatted-settings-edit-form-actions-save-settings"]');
    $this->submitForm([
      'fields[extra_field_example_node_label_formatted][settings_edit_form][settings][link_to_entity]' => FALSE,
      'fields[extra_field_example_node_label_formatted][settings_edit_form][settings][wrapper]' => 'p',
    ], 'Update');
    $session->waitForElementRemoved('css', 'input[id*="edit-fields-extra-field-example-node-label-formatted-settings-edit-form-actions-save-settings"]');
    $page->pressButton('edit-submit');

    $page = $this->drupalGet('/node/' . $this->node->id());
    $session->elementTextEquals('css', 'body > div > div > main > div > div > article > div > div:nth-child(2) > div:nth-child(2) > p', 'VerySpecificTestTitle (from extra_field_plus example)');
  }

  /**
   * Tests the pseudo field Example Node Label Formatted.
   */
  public function testFieldNoLinkSpan() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $driver = $this->getSession()->getDriver();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_example_node_label_formatted', [
        'type' => 'extra_field_example_node_label_formatted',
        'settings' => [
          'link_to_entity' => FALSE,
          'wrapper' => 'p',
        ],
      ])
      ->enable()
      ->save();

    $this->drupalGet('/node/' . $this->node->id());
    $session->elementTextEquals('css', 'body > div > div > main > div > div > article > div > div:nth-child(2) > div:nth-child(2) > p', 'VerySpecificTestTitle (from extra_field_plus example)');
  }

  /**
   * Tests the field with link and h5 settings.
   */
  public function testFieldLinkH5() {
    $session = $this->assertSession();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_example_node_label_formatted', [
        'type' => 'extra_field_example_node_label_formatted',
        'settings' => [
          'link_to_entity' => TRUE,
          'wrapper' => 'h5',
        ],
      ])
      ->enable()
      ->save();

    $this->drupalGet('/node/' . $this->node->id());
    $session->elementExists('css', 'h5 > a[href$="/node/1"]');
    $session->elementTextEquals('css', 'h5 > a[href$="/node/1"]', 'VerySpecificTestTitle (from extra_field_plus example)');
  }

  /**
   * Tests the settings summary changes through ajax.
   */
  public function testSettingsSummary() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_example_node_label_formatted', [
        'type' => 'extra_field_example_node_label_formatted',
        'settings' => [
          'link_to_entity' => FALSE,
          'wrapper' => 'span',
        ],
      ])
      ->enable()
      ->save();
    // Go to display page and check if summary is set with the correct values:
    $this->drupalGet('/admin/structure/types/manage/article/display');
    $session->elementExists('css', 'div.field-plugin-summary');
    $session->elementTextEquals('css', 'div.field-plugin-summary',
      'Link to the entity: No Wrapper: span'
    );

    $page->pressButton('edit-fields-extra-field-example-node-label-formatted-settings-edit');
    $session->waitForElementVisible('css', 'input[id*="edit-fields-extra-field-example-node-label-formatted-settings-edit-form-actions-save-settings"]');
    $this->submitForm([
      'fields[extra_field_example_node_label_formatted][settings_edit_form][settings][link_to_entity]' => TRUE,
      'fields[extra_field_example_node_label_formatted][settings_edit_form][settings][wrapper]' => 'p',
    ], 'Update');
    $session->waitForElementRemoved('css', 'input[id*="edit-fields-extra-field-example-node-label-formatted-settings-edit-form-actions-save-settings"]');

    // @todo This is a workaround and not the later ajax implementation! See:
    // https://www.drupal.org/project/extra_field_plus/issues/3305762
    $session->elementExists('css', 'div.field-plugin-summary');
    $session->elementTextEquals('css', 'div.field-plugin-summary', 'Unsaved changes');
    // @todo Add this later, when the issue is fixed:
    // $session->elementExists('css', 'div.field-plugin-summary');
    // $session->elementTextEquals('css', 'div.field-plugin-summary',
    // 'Link to the entity: Yes Wrapper: p'
    // );
    $page->pressButton('edit-submit');

    $session->elementExists('css', 'div.field-plugin-summary');
    $session->elementTextEquals('css', 'div.field-plugin-summary',
    'Link to the entity: Yes Wrapper: p'
    );
  }

  /**
   * Test to see if the field shows up in Layout Builder.
   */
  public function fieldShowsUpInLayoutBuilder() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $driver = $this->getSession()->getDriver();
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('extra_field_example_node_label_formatted', [
        'type' => 'extra_field_example_node_label_formatted',
        'settings' => [
          'link_to_entity' => FALSE,
          'wrapper' => 'span',
        ],
      ])
      ->enable()
      ->save();
    // Enable layout builder and check if the field exists:
    $this->drupalGet('/admin/structure/types/manage/article/display');
    $page->checkField('edit-layout-enabled');
    $page->pressButton('edit-submit');
    $this->drupalGet('/admin/structure/types/manage/article/display/default/layout');
    $session->pageTextContainsOnce('Placeholder for the "Extra Field Plus: Example Node Label Formatted" field');
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
    $session->pageTextContains('Example Node Label Formatted');
    $this->drupalGet('/layout_builder/add/block/defaults/node.article.default/0/content/extra_field_block%3Anode%3Aarticle%3Aextra_field_example_node_label_formatted');
    $session->elementExists('css', '#edit-settings-label');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-link-to-entity');
    $session->elementExists('css', '#edit-settings-third-party-settings-extra-field-plus-settings-wrapper');
    $page->checkField('edit-settings-third-party-settings-extra-field-plus-settings-link-to-entity');
    $page->fillField('edit-settings-third-party-settings-extra-field-plus-settings-wrapper', 'h4');
    // Save field:
    $page->pressButton('edit-actions-submit');
    // Save layout:
    $page->pressButton('edit-submit');

    // Go to node and check if the field has the correct
    // settings set:
    $this->drupalGet('/node/' . $this->node->id());
    $session->elementExists('css', 'h4 > a[href$="/node/1"]');
    $session->elementTextEquals('css', 'h4 > a[href$="/node/1"]', 'VerySpecificTestTitle (from extra_field_plus example)');
  }

}
