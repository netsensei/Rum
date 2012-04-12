<?php

namespace Rum\Component\Rum\Exception;

class RumNoDatabaseConnectionException extends \Exception {

  function __construct() {
    $message = dt('Could not determine database connection parameters. Pass --db-url option.');
    parent::__construct($message);
  }

}

