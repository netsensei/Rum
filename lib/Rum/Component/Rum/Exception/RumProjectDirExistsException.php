<?php

namespace Rum\Component\Environment\Exception;

class RumProjectDirExistsException extends \Exception {

  function __construct($project_dir) {
    $message = dt('The project directory %project_dir already exists', array('%project_dir' => $project_dir));
    parent::__construct($message);
  }

}
