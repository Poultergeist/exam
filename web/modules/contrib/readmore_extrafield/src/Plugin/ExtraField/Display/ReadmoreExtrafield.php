<?php

namespace Drupal\readmore_extrafield\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayFormattedBase;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Form\FormStateInterface;

/**
 * Readmore Extra field display using extra_field + extra_field_plus.
 *
 * @ExtraFieldDisplay(
 *   id = "readmore_extrafield",
 *   label = @Translation("Read more"),
 *   description = @Translation("Link to the entity"),
 *   bundles = {
 *     "node.*"
 *   },
 *   weight = 100
 * )
 */
class ReadmoreExtrafield extends ExtraFieldPlusDisplayFormattedBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(ContentEntityInterface $entity) {
    $settings = $this->getSettings();

    // Token support:
    $token = FALSE;
    if (\Drupal::moduleHandler()->moduleExists('token')) {
      /** @var \Drupal\Core\Utility\Token $token */
      $token = \Drupal::service('token');
    }

    // Link label:
    $link_title = !empty($settings['link_title']) ? $settings['link_title'] : $this->t('Read more');
    if ($token) {
      $link_title = $token->replace($link_title, [$entity->getEntityTypeId() => $entity], ['clear' => TRUE]);
    }

    $element = [
      '#theme' => 'readmore_extrafield',
      '#title' => $link_title,
      '#url' => Url::fromRoute('entity.node.canonical', [
        'node' => $entity->id(),
      ]),
      '#attributes' => [
        'class' => ['readmore-extrafield-link'],
      ],
      // Provide information about host entity and view_mode:
      '#entity' => $entity,
      '#view_mode' => $this->getViewMode(),
    ];

    // Additional classes:
    if (!empty($settings['link_classes'])) {
      $classesArray = explode(' ', $settings['link_classes']);
      foreach ($classesArray as $class) {
        if ($token) {
          $class = $token->replace($class, [$entity->getEntityTypeId() => $entity], ['clear' => TRUE]);
        }
        $element['#attributes']['class'][] = $class;
      }
    }

    if (!empty($settings['link_attr_title'])) {
      $linkAttrTitle = $settings['link_attr_title'];
      if ($token) {
        $linkAttrTitle = $token->replace($linkAttrTitle, [$entity->getEntityTypeId() => $entity], ['clear' => TRUE]);
      }
      $element['#attributes']['title'] = $linkAttrTitle;
    }

    if (!empty($settings['link_attr_rel'])) {
      $element['#attributes']['rel'] = $settings['link_attr_rel'];
    }

    if (!empty($settings['link_attr_target'])) {
      $element['#attributes']['target'] = $settings['link_attr_target'];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected static function extraFieldSettingsForm(): array {
    $form = parent::extraFieldSettingsForm();

    $tokenSupport = \Drupal::moduleHandler()->moduleExists('token');
    $tokenSupportText = ($tokenSupport ? (' ' . t('You may use tokens.')) : '');

    $form['link_title'] = [
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#description' => t('The label text of the read on hyperlink.'),
    ];
    $form['link_classes'] = [
      '#type' => 'textfield',
      '#title' => t('Link classes'),
      '#description' => t('Allows to set additional classes on the link, separated by space.') . $tokenSupportText,
    ];
    $form['link_attr_title'] = [
      '#type' => 'textfield',
      '#title' => t('Link „title“ attribute'),
      '#description' => t('Allows to set the „title“ attrribute value on the link.') . $tokenSupportText,
    ];
    $form['link_attr_rel'] = [
      '#type' => 'textfield',
      '#title' => t('Link „rel“ attribute'),
      '#description' => t('Allows to set the „rel“ attrribute value on the link.'),
    ];
    $form['link_attr_target'] = [
      '#type' => 'textfield',
      '#title' => t('Link „target“ attribute'),
      '#description' => t('Allows to set the „target“ attrribute value on the link.'),
    ];

    if ($tokenSupport) {
      $form['token_help'] = [
        '#theme' => 'token_tree_link',
        // @todo How can we get the entity type here?
        '#token_types' => [],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected static function defaultExtraFieldSettings(): array {
    $values = parent::defaultExtraFieldSettings();

    $values += [
      'link_title' => t('Read more'),
      'link_classes' => '',
      'link_attr_title' => '',
      'link_attr_rel' => '',
      'link_attr_target' => '',
    ];

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  protected static function settingsSummary(string $field_id, string $entity_type_id, string $bundle, string $view_mode = 'default'): array {
    return [
      t('Label: @value', [
        '@value' => self::getExtraFieldSetting($field_id, 'link_title', $entity_type_id, $bundle, $view_mode),
      ]),
      t('Link classes: @value', [
        '@value' => self::getExtraFieldSetting($field_id, 'link_classes', $entity_type_id, $bundle, $view_mode),
      ]),
      t('Link „title“ attribute: @value', [
        '@value' => self::getExtraFieldSetting($field_id, 'link_attr_title', $entity_type_id, $bundle, $view_mode),
      ]),
      t('Link „rel“ attribute: @value', [
        '@value' => self::getExtraFieldSetting($field_id, 'link_attr_rel', $entity_type_id, $bundle, $view_mode),
      ]),
      t('Link „target“ attribute: @value', [
        '@value' => self::getExtraFieldSetting($field_id, 'link_attr_target', $entity_type_id, $bundle, $view_mode),
      ]),
    ];
  }

}
