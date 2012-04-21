<?php

namespace Rum\Component\Rum\Exception;

class RumNoValidOperatingSystemException extends \Exception {

  function __construct($os) {
    $message = dt('Operating system !space is not a valid operating system.', array('!os' => $os));
    parent::__construct($message);
  }

}
