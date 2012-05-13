<?php

namespace Rum\Component\State\Exception;

class RumRepositoryNotClonedException extends \Exception {

  function __construct($repository, $working_directory) {
    $message = dt('Could not clone !repository to !working_directory.', array('!repository' => $repository, '!working_directory' => $working_directory));
    parent::__construct($message);
  }

}
