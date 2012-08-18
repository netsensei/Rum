<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Rum\Exception\RumClassTypeNotFound;
use Rum\Component\Database\Database;

class RumDatabase extends RumDecorator {

  private $db_server;

  private $file_system;

  private $db_user;

  private $db_cred;

  const RUM_DB_MYSQL = 'MySQL';

  public function __construct($rum) {
    parent::__construct($rum);
    $this->checkSetting('rum-db-type');
    $class_name = drush_get_option('rum-db-type', '');
    switch ($class_name) {
      case self::RUM_DB_MYSQL :
        $this->db_server = Database::getInstance($class_name);
        break;
      default :
        throw new RumClassTypeNotFound($class_name, 'Database');
    }
    $settings = $this->db_server->getSettings();
    $settings += array('rum-db-root-user', 'rum-db-root-pass');
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
    $db_root_user = drush_get_option('rum-db-root-user', '');
    $db_root_pass = drush_get_option('rum-db-root-pass', '');
    $this->db_server->setRootUser($db_root_user, $db_root_pass);
  }

  public function setProjectDbUser($db_user = NULL, $db_cred = NULL) {
    if (is_null($db_user)) {
      $this->db_user = substr($this->getEnvironment() . '_' . strtoupper($this->getProjectName()), 0, 16);
    } else {
      $this->db_user = $db_user;
    }

    if (is_null($db_cred)) {
      $this->db_cred = md5(strrev($db_user));
    } else {
      $this->db_cred = $db_cred;
    }
  }

  public function setProjectDb($database = NULL) {
    if (is_null($database)) {
      $database = substr($this->getEnvironment() . '_' . strtoupper($this->getProjectName()), 0, 16);
    }
    $this->database = $database;
  }

  public function getProjectDbUser() {
    return $this->db_user;
  }

  public function getProjectDbCred() {
    return $this->db_cred;
  }

  public function getProjectDb() {
    return $this->database;
  }

  public function createUser() {
    drush_log(dt('Creating a new database user...'), 'status');
    $this->db_server->createUser($this->db_user, $this->db_cred);
  }

  public function createDatabase() {
    drush_log(dt('Creating a new database ...'), 'status');
    $this->db_server->createDatabase($this->database, $this->db_user,$this->db_cred);
  }

  public function dropUser() {
    $this->db_server->dropUser($this->db_user);
  }

  public function dropDatabase() {
    $this->db_server->dropDatabase($this->database);
  }

}