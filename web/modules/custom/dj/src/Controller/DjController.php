<?php

namespace Drupal\dj\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

/**
 * Returns responses for Dj routes.
 */
class DjController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function myPage() {
    $form = \Drupal::formBuilder()->getForm('Drupal\dj\Form\DjForm');
    $myPage['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('All Cats'),
    ];
    $myPage['form'] = $form;

    return [$myPage, $this->getCatsList()];
  }

  /**
   * Builds the Cats List.
   */
  public function getCatsList() {
    $query = \Drupal::database();
    $result = $query->select('dj', 'e')
      ->fields('e', ['name', 'email', 'image', 'timestamp'])
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
        '#width' => 255,
      ];
      $data[] = [
        'name' => $row->name,
        'email' => $row->email,
        'timestamp' => $row->timestamp,
        'img' => [
          'data' => $cat_image,
        ],
      ];
    }

    $header = ['Name', 'Email', 'Image', 'Data'];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $data,
    ];
    return [
      $build,
    ];
  }

}
