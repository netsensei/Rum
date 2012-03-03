<?php

namespace Rum\Component\FileSystem\Exception;

class FileSystemDirectoryWritableException extends \Exception {

  function __construct($directory) {
    $message = dt('Directory %directory is not writable.', array('%directory' => $directory));
    parent::__construct($message);
  }

}