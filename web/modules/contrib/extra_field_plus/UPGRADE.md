# Upgrading from extra_field_plus 1.x or 2.x to 3.x

extra_field_plus >= 3.x contains breaking changes due to misconceptions in
earlier versions.
When upgrading to 3.x (from 8.x-2.x or 8.x-1.x) you have to adjust your
ExtraFieldPlus Display Plugins to match the new static method names.

For details see the related issues:
- https://www.drupal.org/project/extra_field_plus/issues/3305531
- https://www.drupal.org/project/extra_field_plus/issues/3305767

## Required changes in your ExtraField Display Plugins
The changes are relevant for your extrafields defined, which
- extend ExtraFieldPlusDisplayBase
- extend ExtraFieldPlusDisplayFormattedBase

*You only have to change the methods (visibility, static, name and return type). Their contents stay nearly\* the same!*
**\*Note: You may also have to change the parent:: call in the first line of the code**

### 1. settingsForm
Former
~~~
protected function settingsForm(): array {
  $form = parent::settingsForm();

  [Your existing code]
}
~~~
now becomes:
~~~
protected static function extraFieldSettingsForm(): array {
  $form = parent::extraFieldSettingsForm();

  [Your existing code]
}
~~~

### 1. settingsForm
Former
~~~
protected static function defaultFormValues(): array {
  $values = parent::defaultFormValues();

  [Your existing code]
}
~~~
now becomes:
~~~
protected static function defaultExtraFieldSettings(): array {
  $values = parent::defaultExtraFieldSettings();

  [Your existing code]
}
~~~

Furthermore you MAY add a settingsSummary method, but *this is NOT required.*

This allows to show a summary of the selected settings
on the entity display settings form.
~~~
protected static function settingsSummary(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default') {
  [See extra_field_plus_example for example implementations.]
}
~~~


To see some code examples, you may want to have a look into the
extra_field_plus_example module:
https://git.drupalcode.org/project/extra_field_plus/-/tree/3.x/modules/extra_field_plus_example/src/Plugin/ExtraField/Display

Compare that code, with the old 8.x-2.x implementation, to see the differences,
for upgrading purposes:
https://git.drupalcode.org/project/extra_field_plus/-/tree/8.x-2.x/modules/extra_field_plus_example/src/Plugin/ExtraField/Display
