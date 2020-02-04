<?php

namespace Drupal\carousel_block\Plugin\Block;

use Drupal\carousel_block\Entity\CarouselEntity;
use Drupal\carousel_block\Form\SettingsForm;
use Drupal\carousel_block\Service\CarouselServiceInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\image\Entity\ImageStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a carousel' Block.
 *
 * @Block(
 *   id = "carousel_block",
 *   admin_label = @Translation("Carousel Block")
 * )
 */
class CarouselBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * This will hold ImmutableConfig object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $moduleSettings;


  /**
   * The entity manager interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  protected $carouselService;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Constructs a Connection object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, CarouselServiceInterface $carouselService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleSettings = $config_factory->get('carousel_block.settings');
    $this->entityTypeManager = $entity_type_manager;
    $this->carouselService =$carouselService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('carousel.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [
      '#items' => $this->getCarouselItems(),
      '#theme' => 'carousel_block',
    ];

      $build['#attached'] = [
        'library' => [
          'carousel_block/slick',
        ],
        'drupalSettings' => [
          'carousel' => [
            'settings' => [
              'autoplay' => $this->moduleSettings->get('autoplay'),
              'autoplaySpeed' => $this->moduleSettings->get('autoplaySpeed'),
              'dots' => $this->moduleSettings->get('dots'),
              'arrows' => $this->moduleSettings->get('arrows'),
              'infinite' => $this->moduleSettings->get('infinite'),
              'centerMode' => $this->moduleSettings->get('centerMode')
            ]

          ],
        ],
      ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * @return \Drupal\carousel\Entity\Carousel[]|\Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getCarouselItems() {

    $storage = $this->entityTypeManager->getStorage('carousel_entity');
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $query_result = $storage->getQuery()
      ->condition('langcode',$language)
      ->condition('status', 1)
      ->execute();
    $carousel = CarouselEntity::loadMultiple($query_result);

    if (!empty($carousel)) {
      foreach ($carousel as &$item) {

        $file = $item->getImage();
        $image_style = $this->moduleSettings->get('image_style');
        if (!is_null($file->entity)) {
          if (empty($image_style) || $image_style == SettingsForm::ORIGINAL_IMAGE_STYLE_ID) {
            $item->image_url = file_url_transform_relative(file_create_url($file->entity->getFileUri()));
          }
          else {
            $item->image_url = file_url_transform_relative(ImageStyle::load($image_style)
              ->buildUrl($file->entity->getFileUri()));
          }
        }

      }
    }

    return $carousel;
  }

  /**
   * @inheritDoc
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['entity_type:carousel']);
  }

}
