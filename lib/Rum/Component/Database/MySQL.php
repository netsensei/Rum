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
  
    // Create harmless privilige first so the user is created in case it does not exist.
    $harmless = $this->baseCommand() . "GRANT USAGE ON *.* TO '" . $db_user . "'@'localhost';\"";
    drush_shell_exec($harmless);

    // Drop the user if already exists
    $this->dropUser($db_user);

    // Create user
    $create_command = $this->baseCommand() . "CREATE USER '" . $db_user . "'@'localhost' IDENTIFIED BY '" . $db_cred . "';\"";
    drush_shell_exec($create_command);
  }
  
  public function createDatabase($database, $db_user) {
    // Drop the database entirely before we go on.
    $this->dropDatabase($database);

    $create_command = $this->baseCommand() . "CREATE DATABASE IF NOT EXISTS " . $database . ";\"";
    drush_shell_exec($create_command);

    // Grant priviliges.
    $grant_command = $this->baseCommand() . "GRANT USAGE ON * . * TO '" . $db_user . "'@'localhost' IDENTIFIED BY '" . $db_cred . "' WITH MAX_QUERIES_PER_HOUR 0  MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;\"";
    drush_shell_exec($grant_command);
    $grant_command = $this->baseCommand() . "GRANT ALL PRIVILEGES ON " . $database . " . * TO '" . $db_user . "'@'localhost';\"";
    drush_shell_exec($grant_command);
  }

  public function dropUser($db_user) {
    // Drop user
    $drop_command = $this->baseCommand() . "DROP USER '" . $db_user . "'@'localhost';\"";
    drush_shell_exec($drop_command);
  }
  
  public function dropDatabase($database) {
    $drop_command = $this->baseCommand() . "DROP DATABASE IF EXISTS " . $database . ";\"";
    drush_shell_exec($drop_command);
  }
  
  /**
   * Helper function
   * 
   * This is a helper function which returns a base command with a few preconfigured
   * switches. The command is concatenated with a specific query and run through
   * drush_shell_exec().
   * 
   * @return
   *   A string with the mysql base command
   */
  private function baseCommand() {
    return "mysql -u" . $this->db_root_user ." -p" . $this->db_root_pass . " -Bse \"";
  }

}