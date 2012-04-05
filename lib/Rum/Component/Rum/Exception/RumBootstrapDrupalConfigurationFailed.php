<?php

namespace Rum\Component\Rum\Exception;

class RumProjectDirExistsException extends \Exception {

  function __construct() {
    $message = dt('Failed to set the drupal environment. Make sure there is a viable installation base.');
    parent::__construct($message);
  }

}
