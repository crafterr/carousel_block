<?php

namespace Drupal\carousel_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;

/**
 * Class SettingsForm.
 *
 * Provides a settings form.
 *
 * @package Drupal\carousel\Form
 */
class SettingsForm extends ConfigFormBase {

  const ORIGINAL_IMAGE_STYLE_ID = 'original';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'carousel_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['carousel.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('carousel.settings');
    $form['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#description' => $this->t('Enables Autoplay'),
      '#default_value' => $config->get('autoplay'),
    ];

    $form['autoplaySpeed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Autoplay Speed'),
      '#description' => $this->t('Autoplay Speed in milliseconds'),
      '#default_value' => $config->get('autoplaySpeed'),
      '#states' => array(
        'visible' => array(
          ':input[name="autoplay"]' => ['checked' => TRUE],
        ),
      ),
    ];

    $form['dots'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dots'),
      '#description' => $this->t('Show carousel pagination dots.'),
      '#default_value' => $config->get('dots'),
    ];

    $form['arrows'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Arrows'),
      '#description' => $this->t('Prev/Next Arrows.'),
      '#default_value' => $config->get('arrows'),
    ];


    $form['infinite'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Infinite'),
      '#description' => $this->t("If is checked, looping of the carousel is continuing in infinite."),
      '#default_value' => $config->get('infinite'),
    ];

    $form['centerMode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('CenterMode'),
      '#description' => $this->t("Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts."),
      '#default_value' => $config->get('infinite'),
    ];


    $form['image_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Image style'),
      '#description' => $this->t('Image style for carousel items.'),
      '#options' => $this->getImagesStyles(),
      '#default_value' => $config->get('image_style'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Return images styles.
   *
   * @return array
   *   Image styles list
   */
  protected function getImagesStyles() {
    $styles = ImageStyle::loadMultiple();

    $options = [
      static::ORIGINAL_IMAGE_STYLE_ID => $this->t('Original image'),
    ];
    foreach ($styles as $key => $value) {
      $options[$key] = $value->get('label');
    }

    return $options;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ((bool)$form_state->getValue('autoplay') && empty($form_state->getValue('autoplaySpeed'))) {
      $form_state->setErrorByName('autoplaySpeed', $this->t('This field is required'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('carousel.settings')
      ->set('autoplay',$form_state->getValue('autoplay'))
      ->set('autoplaySpeed',$form_state->getValue('autoplaySpeed'))
      ->set('dots', $form_state->getValue('dots'))
      ->set('arrows', $form_state->getValue('arrows'))
      ->set('infinite', $form_state->getValue('infinite'))
      ->set('centerMode', $form_state->getValue('centerMode'))

      ->set('image_style', $form_state->getValue('image_style'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
