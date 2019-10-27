<?php

namespace Drupal\carousel_block\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Carousel entity entity.
 *
 * @ingroup carousel_block
 *
 * @ContentEntityType(
 *   id = "carousel_entity",
 *   label = @Translation("Carousel entity"),
 *   handlers = {
 *     "storage" = "Drupal\carousel_block\CarouselEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\carousel_block\CarouselEntityListBuilder",
 *     "views_data" = "Drupal\carousel_block\Entity\CarouselEntityViewsData",
 *     "translation" = "Drupal\carousel_block\CarouselEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\carousel_block\Form\CarouselEntityForm",
 *       "add" = "Drupal\carousel_block\Form\CarouselEntityForm",
 *       "edit" = "Drupal\carousel_block\Form\CarouselEntityForm",
 *       "delete" = "Drupal\carousel_block\Form\CarouselEntityDeleteForm",
 *     },
 *     "access" = "Drupal\carousel_block\CarouselEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\carousel_block\CarouselEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "carousel_entity",
 *   data_table = "carousel_entity_field_data",
 *   revision_table = "carousel_entity_revision",
 *   revision_data_table = "carousel_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer carousel entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/carousel_entity/{carousel_entity}",
 *     "add-form" = "/admin/structure/carousel_entity/add",
 *     "edit-form" = "/admin/structure/carousel_entity/{carousel_entity}/edit",
 *     "delete-form" = "/admin/structure/carousel_entity/{carousel_entity}/delete",
 *     "version-history" = "/admin/structure/carousel_entity/{carousel_entity}/revisions",
 *     "revision" = "/admin/structure/carousel_entity/{carousel_entity}/revisions/{carousel_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/carousel_entity/{carousel_entity}/revisions/{carousel_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/carousel_entity/{carousel_entity}/revisions/{carousel_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/carousel_entity/{carousel_entity}/revisions/{carousel_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/carousel_entity",
 *   },
 *   field_ui_base_route = "carousel_entity.settings"
 * )
 */
class CarouselEntity extends RevisionableContentEntityBase implements CarouselEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the carousel_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImage() {

    return $this->get('image');
  }

  /**
   * {@inheritdoc}
   */
  public function setImage($image) {

    $this->set('image', $image);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCaptionText() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    if ($this->hasTranslation($language)) {

      return $this->getTranslation($language)->get('caption_text')->value;
    }
    return $this->get('caption_text')->value;
  }

  public function setCaptionText($caption_text) {
    $this->set('caption_text',$caption_text);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Carousel entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 10,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Carousel entity entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    /**
     * Image Id Field Definition
     */
    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setDescription(t('The carousel image.'))
      ->setTranslatable(true)
      ->setDisplayOptions('form', array(
        'type' => 'image',
        'weight' => 2,
      ));


    /**
     * Caption Text definition
     */
    $fields['caption_text'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Caption Text'))
      ->setDescription(t('The Caption Text of carousel.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setTranslatable(true)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ]);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Carousel entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 4,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
