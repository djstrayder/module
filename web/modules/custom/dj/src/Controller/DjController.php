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
  public function myPage() {
    $form = \Drupal::formBuilder()->getForm('Drupal\dj\Form\DjForm');
    $myPage['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Hello! You can add here a photo of your cat'),
    ];
    $myPage['form'] = $form;

    return $myPage;
  }

}


