<?php

namespace Rum\Component\Rum\Exception;

class RumClassTypeNotFound extends \Exception {

  function __construct($type, $component = 'undefined') {
    $message = dt('The class type !type in component !component was not found.', array('!type' => $hosts_file, '!component' => $component));
    parent::__construct($message);
  }

}
