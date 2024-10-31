<?php

namespace Drupal\extra_field_plus\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayManagerInterface;

/**
 * A controller for providing a report page with available extra field plugins.
 */
class ExtraFieldPluginListController extends ControllerBase {

  /**
   * The extra fields plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $pluginManager;

  /**
   * The extra field plugin form manager.
   *
   * @var \Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayManagerInterface
   */
  protected $extraFieldPlusDisplayManager;

  /**
   * Creates an ExtraFieldPluginListController object.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manger
   *   The extra fields plugin manager.
   * @param \Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayManagerInterface $plugin_extra_field_plus_display
   *   The extra field plus plugin display manager.
   */
  public function __construct(PluginManagerInterface $plugin_manger, ExtraFieldPlusDisplayManagerInterface $plugin_extra_field_plus_display) {
    $this->pluginManager = $plugin_manger;
    $this->extraFieldPlusDisplayManager = $plugin_extra_field_plus_display;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.extra_field_display'),
      $container->get('plugin.manager.extra_field_plus_display')
    );
  }

  /**
   * Provides extra field plugins list.
   */
  public function pluginsList() {
    $row = [];
    $definitions = $this->pluginManager->getDefinitions();
    if (ksort($definitions)) {
      foreach ($definitions as $plugin_id => $definition) {
        $hasSettings = is_subclass_of($definition['class'], ExtraFieldPlusDisplayInterface::class);
        $classArray = explode('\\', $definition['class']);
        $row[] = [
          [
            'data' => $plugin_id,
          ],
          [
            'data' => $definition['label'],
          ],
          [
            'data' => [
              '#markup' => implode('<br/>', $definition['bundles']),
            ],
          ],
          [
            'data' => end($classArray),
          ],
          [
            'data' => $definition['provider'],
          ],
          [
            'data' => $hasSettings ? $this->t('Yes') : $this->t('No'),
          ],
        ];
      }
    }

    $elements = [
      '#type' => 'table',
      '#sticky' => TRUE,
      '#empty' => $this->t('Fields list is empty.'),
      '#header' => [
        [
          'data' => $this->t('Id'),
        ],
        [
          'data' => $this->t('Label'),
        ],
        [
          'data' => $this->t('Bundles'),
        ],
        [
          'data' => $this->t('Class'),
        ],
        [
          'data' => $this->t('Provider'),
        ],
        [
          'data' => $this->t('Settings'),
        ],
      ],
      '#rows' => $row,
    ];

    return $elements;
  }

}
