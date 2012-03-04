<?php

namespace Rum\Component\WebServer\Exception;

class RumHostsFileDoesNotExist extends \Exception {

  function __construct($file) {
    $message = dt('The hosts file at %file does not exist', array('%file' => $file));
    parent::__construct($message);
  }

}
