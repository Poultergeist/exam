<?php

namespace Drupal\extra_field_plus\Plugin;

use Drupal\extra_field\Plugin\ExtraFieldDisplayInterface;

/**
 * Defines an interface for Extra Field Plus Display plugins.
 */
interface ExtraFieldPlusDisplayInterface extends ExtraFieldDisplayInterface {

  /**
   * Return the entity extra field settings.
   *
   * @deprecated in extra_field_plus:3.0.0
   * and is removed from extra_field_plus:4.0.0.
   * Due to conceptual issues. Use getEntityExtraFieldSettings() insted.
   * @see https://www.drupal.org/project/extra_field_plus/issues/3305531
   */
  public function getSettings(): array;

  /**
   * Returns this entity instance extra_field settings.
   *
   * @return array
   *   The extra field settings array.
   */
  public function getEntityExtraFieldSettings(): array;

  /**
   * Returns single entity instance extra_field setting by settings key.
   *
   * @param string $key
   *   The settings key.
   *
   * @return mixed
   *   The settings value.
   */
  public function getEntityExtraFieldSetting(string $key);

  /**
   * Returns all field settings as key-value array.
   *
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
   *   The settings array.
   */
  public static function getExtraFieldSettings(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array;

  /**
   * Returns the field setting with the given $key.
   *
   * @param string $field_id
   *   The field id.
   * @param string $key
   *   The setting id.
   * @param string $entity_type_id
   *   The entity type id, e.g. 'node'.
   * @param string $bundle
   *   The entity type bundle, e.g. 'artcile'.
   * @param string $view_mode
   *   The entity view mode, e.g. 'default', 'full', 'teaser' , ...
   *
   * @return mixed
   *   The setting value for the given key.
   */
  public static function getExtraFieldSetting(string $field_id, string $key, string $entity_type_id, string $bundle, string $view_mode = 'default');

  /**
   * Returns the field settings form.
   *
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
  public static function getExtraFieldSettingsForm(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array;

  /**
   * Returns the field settings form default values.
   *
   * @return array
   *   The form values.
   *   Example: [key_1 => value_1, key_2 => value_2,...].
   */
  public static function getDefaultExtraFieldSettings(): array;

  /**
   * Returns the field settings summary.
   *
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
   *   The field settings summary render array.
   */
  public static function getExtraFieldSettingsSummary(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array;

  /**
   * Returns the type of the field.
   *
   * @return string
   *   The field type. Typically 'extra_field'.
   */
  public static function getExtraFieldComponentType(): string;

  /**
   * Returns the full field component id aka field machine name.
   *
   * @return string
   *   The field machine name.
   */
  public static function getExtraFieldComponentId(string $field_id): string;

}
