<?php

namespace Drupal\dj\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Class DeleteForm.
 *
 * @package Drupal\denist3r\Form
 */
class DeleteCat extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */

  public function getFormId() {
    return 'delete_cat';
  }

  /**
   * {@inheritdoc}
   */

  public $cid;
  /**
   * {@inheritdoc}
   */

  public function getQuestion() {
    return t('Do you want to delete?', ['%cid' => $this->cid]);
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
  public function getConfirmText() {
    return t('Delete this entry');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {

    $this->id = $cid;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database();
    $query->delete('dj')
      ->condition('id', $this->id)
      ->execute();
    $this->messenger()->addStatus($this->t(('Successfully deleted')));
    $form_state->setRedirect('dj.cats');
  }

}
