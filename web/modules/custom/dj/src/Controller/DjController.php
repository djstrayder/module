<?php

namespace Drupal\dj\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

/**
 * Returns responses for Dj routes.
 */
class DjController extends ControllerBase {

  /**
   * Builds the Cats List.
   */
  public function getCatsList() {
    $form = \Drupal::formBuilder()->getForm('Drupal\dj\Form\DjForm');
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
        '#width' => 255,
      ];
      $data[] = [
        'name' => $row->name,
        'email' => $row->email,
        'timestamp' => $row->timestamp,
        'id' => $row->id,
        'edit' => 'Edit',
        'delete' => 'Delete',
        'img' => [
          'data' => $cat_image,
        ],
        'uri' => file_create_url($uri),
      ];
    }
    $header = ['Name', 'Email', 'Data', 'Image'];
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
    ];
    return [
      '#theme' => 'catslist',
      '#form' => $form,
      '#header' => $build,
      '#rows' => $data,
    ];
  }

}
