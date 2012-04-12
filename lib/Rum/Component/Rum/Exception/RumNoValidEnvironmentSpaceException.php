<?php

namespace Rum\Component\Rum\Exception;

class RumNoValidEnvironmentSpaceException extends \Exception {

  function __construct($space) {
    $message = dt('Space !space is not a valid environment space.', array('!space' => $space));
    parent::__construct($message);
  }

}
