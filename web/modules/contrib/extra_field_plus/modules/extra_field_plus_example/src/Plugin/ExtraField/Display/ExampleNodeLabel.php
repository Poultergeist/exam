<?php

namespace Drupal\extra_field_plus_example\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Example Extra field Display.
 *
 * @ExtraFieldDisplay(
 *   id = "example_node_label",
 *   label = @Translation("Extra Field Plus: Example Node Label"),
 *   bundles = {
 *     "node.*"
 *   },
 *   visible = false
 * )
 */
class ExampleNodeLabel extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $settings = $this->getEntityExtraFieldSettings();
    $link_to_entity = $settings['link_to_entity'];
    $wrapper = $settings['wrapper'];
    // Indicate that this is the example:
    $label = $entity->label() . ' (from extra_field_plus example)';
    $url = NULL;

    if ($link_to_entity) {
      $url = $entity->toUrl()->setAbsolute();
      $link = [
        '#type' => 'link',
        '#title' => $label,
        '#url' => $url,
      ];
      $label = \Drupal::service('renderer')->render($link);
    }

    $element = [
      '#type' => 'html_tag',
      '#tag' => $wrapper,
      '#value' => $label,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected static function extraFieldSettingsForm(): array {
    $form = parent::extraFieldSettingsForm();

    $form['link_to_entity'] = [
      '#type' => 'checkbox',
      '#title' => t('Link to the entity'),
    ];

    $form['wrapper'] = [
      '#type' => 'select',
      '#title' => t('Wrapper'),
      '#options' => [
        'span' => 'span',
        'p' => 'p',
        'h1' => 'h1',
        'h2' => 'h2',
        'h3' => 'h3',
        'h4' => 'h4',
        'h5' => 'h5',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected static function defaultExtraFieldSettings(): array {
    $values = parent::defaultExtraFieldSettings();

    $values += [
      'link_to_entity' => FALSE,
      'wrapper' => 'span',
    ];

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  protected static function settingsSummary(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    return [
      t('Link to the entity: @link', [
        '@link' => self::getExtraFieldSetting($field_id, 'link_to_entity', $entity_type_id, $bundle, $view_mode) ? t('Yes') : t('No'),
      ]),
      t('Wrapper: @wrapper', [
        '@wrapper' => self::getExtraFieldSetting($field_id, 'wrapper', $entity_type_id, $bundle, $view_mode),
      ]),
    ];
  }

}
