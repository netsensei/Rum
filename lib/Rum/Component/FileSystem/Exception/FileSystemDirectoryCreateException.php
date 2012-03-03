<?php

namespace Rum\Component\FileSystem\Exception;

class FileSystemDirectoryCreateException extends \Exception {
  
  function __construct($directory) {
    $message = dt('Rum was unable to create %directory', array('%directory' => $directory));
    parent::__construct($message);
  }

}