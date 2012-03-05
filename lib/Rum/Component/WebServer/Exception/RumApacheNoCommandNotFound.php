<?php

namespace Rum\Component\WebServer\Exception;

class RumApacheNoCommandFound extends \Exception {

  function __construct() {
    $message = dt('The Apache binary could not be found.');
    parent::__construct($message);
  }

}
