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
      '#markup' => $this->t('All Cats'),
    ];
    $myPage['form'] = $form;

    return $myPage;
  }

}


