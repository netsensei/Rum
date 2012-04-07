<?php

namespace Rum\Component\Rum\Exception;

class RumNoValidCoreVersionException extends \Exception {

  function __construct() {
    $message = dt('Failed to determine a valid Drupal Core version.');
    parent::__construct($message);
  }

}
