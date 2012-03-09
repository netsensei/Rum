<?php

namespace Rum\Component\Database;

abstract class Database {
  
  protected $db_root_user;

  protected $db_root_pass;

  protected static $instance = NULL;

  final public function getInstance($class_name) {
    $class_name = 'Rum\\Component\\Database\\' . $class_name;

    if (!self::$instance) {
      self::$instance = new $class_name();
    }

    return self::$instance;
  }

  public function setRootUser($db_root_user, $db_root_pass) {
    $this->db_root_user = $db_root_user;
    $this->db_root_pass = $db_root_pass;
  }

  abstract public function getSettings();
 
  abstract public function createUser($db_user, $db_cred);

}