<?php

namespace Rum\Component\Rum\Exception;

class RumHostsFileDoesNotExist extends \Exception {

  function __construct($hosts_file) {
    $message = dt('The hosts file !hosts_file does not exist', array('!hosts_file' => $hosts_file));
    parent::__construct($message);
  }

}
