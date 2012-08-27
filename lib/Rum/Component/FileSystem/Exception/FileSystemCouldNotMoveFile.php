<?php

namespace Rum\Component\FileSystem\Exception;

class FileSystemCouldNotMoveFile extends \Exception {

  function __construct($source, $target) {
    $message = dt('Rum could not move !source to !target', array('!source' => $source, '!target' => $target));
    parent::__construct($message);
  }

}