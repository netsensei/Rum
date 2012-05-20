<?php

namespace Rum\Component\State;

abstract class State {

  protected static $instance = NULL;

  final public function getInstance($class_name) {
    $class_name = 'Rum\\Component\\State\\' . $class_name;

    if (!self::$instance) {
      self::$instance = new $class_name();
    }

    return self::$instance;
  }

  abstract public function fetch($repository, $working_copy);

  abstract public function createIgnoreFile($working_copy);

  abstract public function getIgnoreFile();

}