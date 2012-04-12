<?php

namespace Rum\Component\Rum\Exception;

class RumNoValidProjectNameException extends \Exception {

  function __construct() {
    $message = dt('Cannot set an empty string as a project name.');
    parent::__construct($message);
  }

}
