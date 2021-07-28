<?php

namespace Drupal\dj\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;

/**
 * Implements an Edit Form.
 */
class EditCat extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_cat';
  }

  /**
   * ID of the item to delete.
   *
   * @let. this is dj.
   */
  protected $id;
  /**
   * {@inheritdoc}
   */
  public $cid;

  /**
   * {@inheritdoc}
   */
  public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete it!');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('dj.cats');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you want to edit %cid?', ['%cid' => $this->cid]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
    $this->id = $cid;
    $query = \Drupal::database();
    $data = $query->select('dj', 'e')
      ->condition('e.id', $cid, '=')
      ->fields('e', ['id', 'name', 'email', 'image'])
      ->execute()->fetchAll();
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#description' => $this->t('minimum length 2, maximum length 32'),
      '#required' => TRUE,
      '#default_value' => $data[0]->name,
      '#ajax' => [
        'callback' => '::setMessageCat',
        'event' => 'change',
      ],
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#description' => $this->t('the name can only contain latin letters, underscores, or hyphens.'),
      '#required' => TRUE,
      '#default_value' => $data[0]->email,
      '#ajax' => [
        'callback' => '::setMessageEmailN',
        'event' => 'change',
      ],
    ];
    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Add a photo of your cat'),
      '#required' => TRUE,
      '#default_value' => $data[0]->image,
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpeg jpg png'],
        'file_validate_size' => ['2097152'],
      ],
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Edit cat'),
      '#button_type' => 'submit',
      '#ajax' => [
        'callback' => '::setMessage',
        'event' => 'click',
      ],
    ];
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $emailVl = $form_state->getValue('email');
    if (strlen($form_state->getValue('name')) < 2) {
      $form_state->setErrorByName('name', $this->t('The your name is too short. Please enter a  full name.'));
    }
    if ((!filter_var($emailVl, FILTER_VALIDATE_EMAIL)) || (strpbrk($emailVl, '1234567890+*/!#$^&*()='))) {
      $form_state->setErrorByName('email', $this->t('The your email not correct'));
    }
  }

  /**
   * Our custom Ajax.
   */
  public function setMessageCat(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (strlen($form_state->getValue('name')) < 2) {
      $response->addCommand(
        new HtmlCommand(
          '.result_message',
          'Your name is too short'
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.result_message',
          'Your name is: ' . $form_state->getValue('name')
        )
      );
    }
    return $response;
  }

  /**
   * Our custom Ajax.
   */
  public function setMessageEmailN(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $emailVl = $form_state->getValue('email');
    if ((!filter_var($emailVl, FILTER_VALIDATE_EMAIL)) || (strpbrk($emailVl, '1234567890+*/!#$^&*()='))) {
      $response->addCommand(
        new HtmlCommand(
          '.result_message',
          'Your email not correct'
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.result_message',
          'Your email: ' . $form_state->getValue('email')
        )
      );
    }
    return $response;
  }

  /**
   * Our custom Ajax.
   */
  public function setMessage(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $form_state->setRedirect('dj.cats');
    if ($form_state->hasAnyErrors()) {
      return $response;
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.result_message',
          'Your cat’s name: ' . $form_state->getValue('name') . '.<br>' .
          'Your email: ' . $form_state->getValue('email')
        )
      );
    }
    $response->addCommand(new RedirectCommand('/dj/cats'));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('Your name is @name', ['@name' => $form_state->getValue('cat_name')]));
    $this->messenger()->addStatus($this->t('Your email is @email', ['@email' => $form_state->getValue('email')]));
    $picture = $form_state->getValue('image');
    $file = File::load($picture[0]);
    $file->setPermanent();
    $file->save();
    \Drupal::database()->update('dj')
      ->condition('id', $this->id)
      ->fields([
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
        'image' => $picture[0],
      ])
      ->execute();

  }

}
