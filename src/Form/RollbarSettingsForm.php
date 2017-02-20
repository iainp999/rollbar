<?php

namespace Drupal\rollbar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for rollbar settings.
 */
class RollbarSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rollbar_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['rollbar.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rollbar.settings');

    $form['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $config->get('enabled'),
    );

    $form['access_token'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Access Token'),
      '#default_value' => $config->get('access_token'),
      '#required' => TRUE,
    );

    $form['capture_uncaught'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Capture Uncaught'),
      '#default_value' => $config->get('capture_uncaught'),
    );

    $form['capture_unhandled_rejections'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Capture uncaught rejections'),
      '#default_value' => $config->get('capture_unhandled_rejections'),
    );

    $form['environment'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Environment'),
      '#default_value' => $config->get('environment'),
      '#description' => $this->t('The environment string to use when reporting errors'),
    );

    $form['rollbar_js_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Rollbar JS URL'),
      '#default_value' => $config->get('rollbar_js_url'),
      '#description' => $this->t('The URL to the Rollbar js library'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('rollbar.settings')
      ->set('access_token', $form_state->getValue('access_token'))
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('capture_uncaught', $form_state->getValue('capture_uncaught'))
      ->set('capture_unhandled_rejections', $form_state->getValue('capture_unhandled_rejections'))
      ->set('environment', $form_state->getValue('environment'))
      ->set('rollbar_js_url', $form_state->getValue('rollbar_js_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
