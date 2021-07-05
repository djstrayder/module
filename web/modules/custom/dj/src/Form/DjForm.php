<?php

namespace Drupal\dj\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Implements an example form.
 */
class DjForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>'
    ];

    $form['cat_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#description' =>$this->t('minimum length 2, maximum length 32'),
      '#required ' => true,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add cat'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::setMessage',
      ]
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('cat_name')) <= 2) {
      $form_state->setErrorByName('cat_name', $this->t('The your name is too short. Please enter a  full name.'));
    }
    if (strlen($form_state->getValue('cat_name')) > 32) {
      $form_state->setErrorByName('cat_name', $this->t('The your name is too long. Please enter a full name.'));
    }

  }


  /**
   * Our custom Ajax.
   */

  public function setMessage(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result_message',
        'Your cat’s name: ' . $form_state->getValue('cat_name')
      )
    );
    return $response;
  }



  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('Your name is @cat_name', ['@cat_name' => $form_state->getValue('cat_name')]));

  }

}
