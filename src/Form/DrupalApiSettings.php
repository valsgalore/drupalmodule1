<?php

namespace Drupal\drupal_api\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

class DrupalApiSettings extends ConfigFormBase {

  // Return editable config bin for drupal_api
  protected function getEditableConfigNames()  {
    return [
      'drupal_api.config',
    ];
  }

  public function getFormId() {
    return 'drupal_api.settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    //Read-only config bin object
    $config = $this->configFactory->get('drupal_api.config');

    $form['automatic'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update list automatically'),
      '#default_value' => $config->get('automatic'),
    ];
    $form['interval'] = [
      '#type' => 'select',
      '#title' => $this->t('Fetch Interval'),
      '#options' => [
        1 => $this->t('@count second', ['@count' => 1]),
        1800 => $this->t('@count minutes', ['@count' => 30]),
        3600 => $this->t('@count hour', ['@count' => 1]),
        6400 => $this->t('@count hours', ['@count' => 2]),
        18000 => $this->t('@count hours', ['@count' => 5]),
        36000 => $this->t('@count hours', ['@count' => 10]),
        86400 => $this->t('@count hours', ['@count' => 24]),
      ],
      '#default_value' => $config->get('interval'),
      '#description' => $this->t('How often to retrieve new projects from Drupal.org.'),
      '#states' => [
        'visible' => [
          ':input[name="automatic"]' => ['checked' => TRUE],
        ],
      ],
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Settings'),
      '#weight' => 100,
    ],
    $form['update'] = [
      '#type' => 'submit',
      '#name' => 'update',
      '#value' => $this->t('Update list now'),
      '#weight' => 105,
    ],
  ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)  {

    if ($form_state->getTriggeringElement()['#name'] == 'update') {
      //Run fetch function from Drupal API Manager
      \Drupal::service('drupal_api.manager')->fetchLatestProjects();
      \Drupal::messenger()->addMessage('Newest Drupal projects fetched.');
      //Provides link to updated list of projects
      $link = \Drupal\Core\Url::fromRoute('drupal_api.latest_modules');
      $message = 'See the updated list at <a href="' . $link->toString() . '">Drupal Modules List</a>';
      \Drupal::messenger()->addMessage($this->t($message));
    }

    else {
      //Run submit functions from parent class (ConfigFormBase)
      parent::submitForm($form, $form_state);

      //Editable config bin object
      $config = $this->config('drupal_api.config');
      $config->set('interval', $form_state->getValue('interval'))
        ->set('automatic', $form_state->getValue('automatic'))
        ->save();
    }
  }

}
