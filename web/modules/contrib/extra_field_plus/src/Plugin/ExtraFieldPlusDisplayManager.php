<?php

namespace Drupal\extra_field_plus\Plugin;

use Drupal\extra_field\Plugin\ExtraFieldDisplayManager;

/**
 * Manages Extra Field Plus plugins display settings forms.
 */
class ExtraFieldPlusDisplayManager extends ExtraFieldDisplayManager implements ExtraFieldPlusDisplayManagerInterface {

  /**
   * The interface each plugin should implement.
   */
  const PLUGIN_INTERFACE = 'Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayInterface';

  /**
   * {@inheritdoc}
   */
  public function fieldInfo(): array {
    // General fieldInfo is prepared in parent class.
    $info = parent::fieldInfo();
    // We need to loop the array again to add the plugin_id.
    // @todo If possible the plugin_id should be added in extra_field so we
    // don't have to do this loop again
    // @see https://www.drupal.org/project/extra_field_plus/issues/3306023
    // Once that's fixed, this whole method can be removed!
    $definitions = $this->getDefinitions();
    foreach ($definitions as $pluginId => $definition) {
      $entityBundles = $this->supportedEntityBundles($definition['bundles']);
      foreach ($entityBundles as $entityBundle) {
        $entityType = $entityBundle['entity'];
        $bundle = $entityBundle['bundle'];
        $fieldName = $this->fieldName($pluginId);
        $info[$entityType][$bundle]['display'][$fieldName]['plugin_id'] = $pluginId;
      }
    }
    return $info;
  }

  /**
   * Returns the field setting with the given $key.
   *
   * @param string $pluginId
   *   The Plugin Id to load the settings from.
   * @param string $field_id
   *   The field id.
   * @param string $entity_type_id
   *   The entity type id, e.g. 'node'.
   * @param string $bundle
   *   The entity type bundle, e.g. 'article'.
   * @param string $view_mode
   *   The entity view mode, e.g. 'default', 'full', 'teaser' , ...
   *
   * @return array
   *   The setting array.
   */
  public function getSettings(string $pluginId, string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    $plugin = $this->getFieldPlugin($pluginId);
    $pluginClass = get_class($plugin);
    return $pluginClass::getExtraFieldSettings($field_id, $entity_type_id, $bundle, $view_mode);
  }

  /**
   * Returns the field settings form.
   *
   * @param string $pluginId
   *   The Plugin Id to load the settings from.
   * @param string $field_id
   *   The field id.
   * @param string $entity_type_id
   *   The entity type id, e.g. 'node'.
   * @param string $bundle
   *   The entity type bundle, e.g. 'artcile'.
   * @param string $view_mode
   *   The entity view mode, e.g. 'default', 'full', 'teaser' , ...
   *
   * @return array
   *   The field settings form array.
   *   Example: [key_1 => [...], key_2 => [...], ...].
   */
  public function getSettingsForm(string $pluginId, string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    $plugin = $this->getFieldPlugin($pluginId);
    if ($plugin === NULL) {
      return [];
    }
    $pluginClass = get_class($plugin);
    return $pluginClass::getExtraFieldSettingsForm($field_id, $entity_type_id, $bundle, $view_mode);
  }

  /**
   * Returns the field settings summary.
   *
   * @param string $pluginId
   *   The Plugin Id to load the settings from.
   * @param string $field_id
   *   The field id.
   * @param string $entity_type_id
   *   The entity type id, e.g. 'node'.
   * @param string $bundle
   *   The entity type bundle, e.g. 'article'.
   * @param string $view_mode
   *   The entity view mode, e.g. 'default', 'full', 'teaser' , ...
   *
   * @return array
   *   The field settings summary render array.
   */
  public function getSettingsSummary(string $pluginId, string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    $plugin = $this->getFieldPlugin($pluginId);
    if ($plugin === NULL) {
      return [];
    }
    $pluginClass = get_class($plugin);
    return $pluginClass::getExtraFieldSettingsSummary($field_id, $entity_type_id, $bundle, $view_mode);
  }

  /**
   * Helper function to return the field plugin by the given plugin id.
   *
   * @param string $pluginId
   *   The plugin id.
   *
   * @return ExtraFieldPlusDisplayInterface|null
   *   The extra field plus display plugin.
   */
  protected function getFieldPlugin(string $pluginId): ?ExtraFieldPlusDisplayInterface {
    // Add settings and settings Form:
    /** @var \Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayInterface $plugin */
    $plugin = $this->getFactory()->createInstance($pluginId);
    if (!($plugin instanceof ExtraFieldPlusDisplayInterface)) {
      return NULL;
    }
    return $plugin;
  }

}
