<?php

namespace Rum\Component\Rum\Exception;

class RumNoValidProjectDirException extends \Exception {

  function __construct() {
    $message = dt('Cannot set an empty string as a project directory.');
    parent::__construct($message);
  }

}
