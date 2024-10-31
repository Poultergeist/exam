<?php

namespace Drupal\extra_field_plus\Plugin;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * Base class for Extra field Plus Display plugins.
 */
abstract class ExtraFieldPlusDisplayBase extends ExtraFieldDisplayBase implements ExtraFieldPlusDisplayInterface {
  use StringTranslationTrait;
  use ExtraFieldPlusDisplayTrait;

  /**
   * {@inheritdoc}
   */
  protected static function extraFieldSettingsForm(): array {
    $elements = [];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected static function defaultExtraFieldSettings(): array {
    return [];
  }

}
