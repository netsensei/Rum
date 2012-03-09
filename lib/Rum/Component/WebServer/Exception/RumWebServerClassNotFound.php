<?php

namespace Rum\Component\WebServer\Exception;

class RumWebServerClassNotFound extends \Exception {

  function __construct($class_name) {
    $message = dt('The webserver type %type could not be found.', array('%type' => $class_name));
    parent::__construct($message);
  }

}
