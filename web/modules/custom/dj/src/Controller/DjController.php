<?php

namespace Drupal\dj\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Dj routes.
 */
class DjController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Hello! You can add here a photo of your cat'),
    ];

    return $build;
  }

}
