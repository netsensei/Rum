<?php

namespace Rum\Component\Rum\Exception;

class RumNoValidProjectDomainException extends \Exception {

  function __construct() {
    $message = dt('Cannot set an empty string as a project domain.');
    parent::__construct($message);
  }

}
