<?php

namespace Rum\Component\State\Exception;

class RumRepositoryNotCheckedOutException extends \Exception {

  function __construct($repository, $working_directory) {
    $message = dt('Could not check out !repository to !working_directory.', array('!repository' => $repository, '!working_directory' => $working_directory));
    parent::__construct($message);
  }

}
