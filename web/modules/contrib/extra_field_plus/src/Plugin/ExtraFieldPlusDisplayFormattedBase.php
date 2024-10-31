<?php

namespace Drupal\extra_field_plus\Plugin;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayFormattedBase;
use Drupal\extra_field\Plugin\ExtraFieldDisplayFormattedInterface;

/**
 * Base class for Extra Field Plus Display plugins with field wrapper output.
 */
abstract class ExtraFieldPlusDisplayFormattedBase extends ExtraFieldDisplayFormattedBase implements ExtraFieldPlusDisplayInterface, ExtraFieldDisplayFormattedInterface {
  use StringTranslationTrait;
  use ExtraFieldPlusDisplayTrait;

  /**
   * {@inheritDoc}
   */
  protected static function extraFieldSettingsForm(): array {
    $elements = [];

    return $elements;
  }

  /**
   * {@inheritDoc}
   */
  protected static function defaultExtraFieldSettings(): array {
    return [];
  }

}
