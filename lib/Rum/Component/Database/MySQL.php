<?php

namespace Rum\Component\Database;

use Rum\Component\Database\Database;

class MySQL extends Database {

  protected $db_root_user;

  protected $db_root_pass;

  public function getSettings() {
    return array();
  }

  /**
   * Creates a new MySQl user
   * 
   * @todo Figure out how to write this in a cleaner way. With error handling.
   * 
   * @param type $db_user
   * @param type $db_cred 
   */
  public function createUser($db_user, $db_cred) {

    $start_command = $this->baseCommand();
  
    // Create harmless privilige first so the user is created in case it does not exist.
    $harmless = $start_command . "GRANT USAGE ON *.* TO '" . $db_user . "'@'localhost';\"";
    drush_shell_exec($harmless);

    // Drop user and db.
    $drop_command = $start_command . "DROP USER '" . $db_user . "'@'localhost';\"";
    drush_shell_exec($drop_command);
    $drop_command = $start_command . "DROP DATABASE IF EXISTS " . $db_user . ";\"";
    drush_shell_exec($drop_command);

    // Create user and db.
    $create_command = $start_command . "CREATE USER '" . $db_user . "'@'localhost' IDENTIFIED BY '" . $db_cred . "';\"";
    drush_shell_exec($create_command);
    $create_command = $start_command . "CREATE DATABASE IF NOT EXISTS " . $db_user . ";\"";
    drush_shell_exec($create_command);

    // Grant priviliges.
    $grant_command = $start_command . "GRANT USAGE ON * . * TO '" . $db_user . "'@'localhost' IDENTIFIED BY '" . $db_cred . "' WITH MAX_QUERIES_PER_HOUR 0  MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;\"";
    drush_shell_exec($grant_command);
    $grant_command = $start_command . "GRANT ALL PRIVILEGES ON " . $db_user . " . * TO '" . $db_user . "'@'localhost';\"";
    drush_shell_exec($grant_command);
  }

  private function baseCommand() {
    return "mysql -u" . $this->db_root_user ." -p" . $this->db_root_pass . " -Bse \"";
  }

}