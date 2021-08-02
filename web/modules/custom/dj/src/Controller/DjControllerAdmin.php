<?php

namespace Drupal\dj\Controller;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Returns responses for Dj routes.
 */
class DjControllerAdmin extends FormBase {

  /**
   * {@inheritdoc}
   */
  public $cid;

  /**
   * Cat list.
   *
   * {inheriddoc}.
   */
  public function buildForm($form, $form_state, $cid = NULL) {
    $this->id = $cid;
    $query = \Drupal::database();
    $result = $query->select('dj', 'e')
      ->fields('e', ['id', 'name', 'email', 'image', 'timestamp'])
      ->orderBy('timestamp', 'DESC')
      ->execute()->fetchAll();
    $data = [];
    foreach ($result as $row) {
      $file = File::load($row->image);
      $uri = $file->getFileUri();
      $cat_image = [
        '#theme' => 'image',
        '#uri' => $uri,
        '#alt' => 'Cat Photo',
        '#title' => 'Cat',
        '#width' => 100,
      ];
      $data[$row->id] = [
        $row->id,
        $row->name,
        $row->email,
        $row->timestamp,
        [
          'data' => $cat_image,
        ],
        t("<a href='/dj/admin/edit-cat/$row->id' class='use-ajax catlink' data-dialog-type='modal'>Edit</a>"),
        t("<a href='/dj/admin/delete-cat/$row->id' class='use-ajax catlink' data-dialog-type='modal' >Delete</a>"),
      ];
    }
    $header = ['id', 'Name', 'Email', 'Timestamp', 'Image', 'Edit', 'Delete'];
    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $data,
      '#empty' => $this->t('No cats found'),
    ];
    $form['delete cats'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#button_type' => 'submit',
      '#attributes' => ['onclick' => 'if(!confirm("Are you sure about that?")){return false;}'],
    ];
    return $form;
  }

  /**
   * Form func.
   *
   * {inheriddoc}.
   */
  public function getFormId() {
    return "dj_admin";
  }

  /**
   * Form func.
   *
   * {inheriddoc}.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues('table')['table'];
    $delete = array_filter($values);
    if ($delete == NULL) {
      $form_state->setRedirect('dj.DeleteCat');
    }
    else {
      $query = \Drupal::database();
      $query->delete('dj')
        ->condition('id', $delete, 'IN')
        ->execute();
      $this->messenger()->addStatus($this->t("Successfully deleted"));
    }
  }

}
