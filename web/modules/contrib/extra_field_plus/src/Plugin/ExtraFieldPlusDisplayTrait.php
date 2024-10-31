<?php

namespace Drupal\extra_field_plus\Plugin;

use Drupal\Core\Render\Element;
use Drupal\Component\Plugin\PluginBase;
use Drupal\layout_builder\LayoutBuilderEnabledInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;

/**
 * Base class for Extra field Plus Display plugins.
 */
trait ExtraFieldPlusDisplayTrait {

  /**
   * Return the entity extra field settings.
   *
   * @deprecated in extra_field_plus:3.0.0
   * and is removed from extra_field_plus:3.0.0.
   * Due to conceptual issues. Use getEntityExtraFieldSettings() insted.
   * @see https://www.drupal.org/project/extra_field_plus/issues/3305531
   */
  public function getSettings(): array {
    @trigger_error('getSettings() is deprecated in extra_field_plus:3.0.0 and is removed from extra_field_plus:3.0.0. Replace by static function extraFieldSettingsForm(). See https://www.drupal.org/project/extra_field_plus/issues/3305531', E_USER_DEPRECATED);
    return $this->getEntityExtraFieldSettings();
  }

  /**
   * Return the entity extra field settings form.
   *
   * @deprecated in extra_field_plus:3.0.0
   * and is removed from extra_field_plus:3.0.0.
   * Due to conceptual issues. Use static extraFieldSettingsForm() insted.
   * @see https://www.drupal.org/project/extra_field_plus/issues/3305531
   */
  protected function settingsForm(): array {
    @trigger_error('settingsForm() is deprecated in extra_field_plus:3.0.0 and is removed from extra_field_plus:3.0.0. Replace by static function extraFieldSettingsForm(). See https://www.drupal.org/project/extra_field_plus/issues/3305531', E_USER_DEPRECATED);
    return static::extraFieldSettingsForm();
  }

  /**
   * Return the entity default form values.
   *
   * @deprecated in extra_field_plus:3.0.0
   * and is removed from extra_field_plus:3.0.0.
   * Due to conceptual issues. Use static defaultExtraFieldSettings() insted.
   * @see https://www.drupal.org/project/extra_field_plus/issues/3305531
   */
  protected function defaultFormValues(): array {
    @trigger_error('defaultFormValues() is deprecated in extra_field_plus:3.0.0 and is removed from extra_field_plus:3.0.0. Replace by static function defaultExtraFieldSettings(). See https://www.drupal.org/project/extra_field_plus/issues/3305531', E_USER_DEPRECATED);
    return static::defaultExtraFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityExtraFieldSettings(): array {
    $field_id = static::getExtraFieldComponentId($this->getPluginId());
    $entity_type = $this->getEntity()->getEntityTypeId();
    $bundle = $this->getEntity()->bundle();
    $view_mode = $this->getViewMode();

    return static::getExtraFieldSettings($field_id, $entity_type, $bundle, $view_mode);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityExtraFieldSetting(string $key) {
    $settings = $this->getEntityExtraFieldSettings();
    return $settings[$key] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function getExtraFieldSettings(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    $settings = [];
    $default_settings = (array) static::getDefaultExtraFieldSettings();
    $componentId = static::getExtraFieldComponentId($field_id);

    // Try to load explictly this  entity view display:
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $view_display */
    $entityViewDisplay = \Drupal::entityTypeManager()->getStorage('entity_view_display')->load($entity_type_id . '.' . $bundle . '.' . $view_mode);
    if (!$entityViewDisplay) {
      // Fall back to 'default' if this view mode does not exist:
      $entityViewDisplay = \Drupal::service('entity_display.repository')->getViewDisplay($entity_type_id, $bundle);
    }

    // Layout Builder support:
    // Note: EntityDisplayBase components are quite different (array) from
    // layout builder components (objects)! So we have to handle their data
    // differently.
    if ($entityViewDisplay instanceof LayoutBuilderEnabledInterface && $entityViewDisplay->isLayoutBuilderEnabled()) {
      // @todo This might be improved, as looping all sections and components
      // is inifficient.
      /** @var \Drupal\layout_builder\LayoutBuilderEntityViewDisplay $entityViewDisplay */
      $fieldSectionComponent = static::getSectionComponentForFieldName($entityViewDisplay, $field_id);
      if (!empty($fieldSectionComponent)) {
        $sectionComponentPlugin = $fieldSectionComponent->getPlugin();
        $sectionComponentPluginDefinitionId = $sectionComponentPlugin->getPluginDefinition()['id'];
        if ($sectionComponentPluginDefinitionId === 'extra_field_block') {
          $settings = $fieldSectionComponent->get('extra_field_plus_settings') ?? [];
        }
        else {
          throw new \Exception('Plugin definition id "extra_field_block" expected for field ' . $field_id . ' but was ' . $sectionComponentPluginDefinitionId);
        }
      }
    }
    else {
      // Regular display (EntityDisplayBase component array)
      $component = $entityViewDisplay->getComponent($componentId);
      if (empty($component)) {
        // The component is not defined on this entity display.
        // Return default values.
        return $default_settings;
      }
      $settings = $component['settings'] ?? [];
    }
    return array_merge($default_settings, $settings);
  }

  /**
   * Replicates LayoutBuilderEntityViewDisplay::getSectionComponentForFieldName.
   *
   * Gets the component for a given field name if any.
   * LayoutBuilderEntityViewDisplay::getSectionComponentForFieldName is private,
   * so we have to replicate it here until its visibility is changed to
   * public.
   *
   * @param Drupal\layout_builder\LayoutBuilderEntityViewDisplay $entityViewDisplay
   *   The $entityViewDisplay to search in.
   * @param string $field_name
   *   The field name.
   *
   * @return \Drupal\layout_builder\SectionComponent|null
   *   The section component if it is available.
   *
   * @see https://www.drupal.org/project/drupal/issues/3305970
   * @see https://api.drupal.org/api/drupal/core!modules!layout_builder!src!Entity!LayoutBuilderEntityViewDisplay.php/function/LayoutBuilderEntityViewDisplay%3A%3AgetSectionComponentForFieldName/9.5.x
   */
  protected static function getSectionComponentForFieldName(LayoutBuilderEntityViewDisplay $entityViewDisplay, $field_name) {

    // Loop through every component until the first match is found.
    foreach ($entityViewDisplay
      ->getSections() as $section) {
      foreach ($section
        ->getComponents() as $component) {
        $plugin = $component
          ->getPlugin();
        if ($plugin instanceof DerivativeInspectionInterface && in_array($plugin
          ->getBaseId(), [
          // 'field_block', we don't need this one here.
            'extra_field_block',
          ], TRUE)) {

          // FieldBlock derivative IDs are in the format
          // [entity_type]:[bundle]:[field].
          [,,
            $field_block_field_name,
          ] = explode(PluginBase::DERIVATIVE_SEPARATOR, $plugin
            ->getDerivativeId());
          if ($field_block_field_name === $field_name) {
            return $component;
          }
        }
      }
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function getExtraFieldSetting(string $field_id, string $key, string $entity_type_id, string $bundle, string $view_mode = 'default') {
    $settings = static::getExtraFieldSettings($field_id, $entity_type_id, $bundle, $view_mode);

    return $settings[$key] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function getExtraFieldSettingsForm(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    $default_values = (array) static::getDefaultExtraFieldSettings();
    $elements = (array) static::extraFieldSettingsForm();

    if (!empty($elements)) {
      foreach (Element::children($elements) as $name) {
        if (!isset($elements[$name]['#default_value']) && isset($default_values[$name])) {
          $elements[$name]['#default_value'] = $default_values[$name];
        }
      }
    }

    return $elements;
  }

  /**
   * Returns the extra field display settings form.
   *
   * @return array
   *   FAPI array.
   *   Example: [key_1 => [...], key_2 => [...], ...].   *
   */
  abstract protected static function extraFieldSettingsForm(): array;

  /**
   * {@inheritdoc}
   */
  public static function getDefaultExtraFieldSettings(): array {
    return static::defaultExtraFieldSettings();
  }

  /**
   * Returns the extra field default display settings.
   *
   * @return array
   *   Key-value array of the default values.
   *   Example: [key_1 => value_1, key_2 => value_2,...].
   */
  abstract protected static function defaultExtraFieldSettings(): array;

  /**
   * Provides the field settings summary.
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
   * @return string[]|\Drupal\Core\StringTranslation\TranslatableMarkup[]
   *   An array of field settings summary strings.
   */
  protected static function settingsSummary(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    return [
      t('This extra field has settings'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function getExtraFieldSettingsSummary(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    return [
      '#type' => 'inline_template',
      '#template' => '<div class="field-plugin-summary">{{ summary|safe_join("<br />") }}</div>',
      '#context' => ['summary' => static::settingsSummary($field_id, $entity_type_id, $bundle, $view_mode)],
    ];
  }

  /**
   * Gets a field setting from the form state.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param string $setting
   *   The setting name to get.
   * @param string $default
   *   The default value, if a setting is not set.
   *
   * @return mixed
   *   The field setting value.
   */
  protected function getSummarySetting(FormStateInterface $form_state, $setting, $default = '') {
    $settings = $form_state->getStorage();
    return $settings[$this->fieldNameFromPluginId()][$setting] ?? $default;
  }

  /**
   * {@inheritdoc}
   */
  public static function getExtraFieldComponentType(): string {
    return 'extra_field';
  }

  /**
   * {@inheritdoc}
   */
  public static function getExtraFieldComponentId(string $field_id): string {
    // Ensure the prefix is never added twice:
    // @todo This should be from an extra_field constant, see:
    // https://www.drupal.org/project/extra_field/issues/3305672
    $field_id = str_replace('extra_field_', '', $field_id);
    return 'extra_field_' . $field_id;
  }

}
