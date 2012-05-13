<?php
/**
 * @file
 * Contains the MySQL driver for Rum
 *
 * The class in this file adds MySQL support to Rum through a concrete implementation
 * of the Database class which can be instantiated.
 */

namespace Rum\Component\Database;

use Rum\Component\Database\Database;

/**
 * MySQL driver class
 *
 * This class extends and implements the abstract Database class. It provides
 * a concrete implementation to connect and perform actions against a MySQL
 * database system.
 */
class MySQL extends Database {

  protected $db_root_user;

  protected $db_root_pass;

  public function getSettings() {
    return array();
  }

  /**
   * Creates a new MySQl user and database
   *
   * When creating a new database user, a corresponding database will be created
   * and the user will be granted all privileges on this database. Each database
   * user can only access its' corresponding database rather then using a general
   * account which is granted privileges to every database. Before we create
   * the user and database, the function drops any existing user and database
   * before creating them anew.
   *
   * @todo Figure out how to write this in a cleaner way. With error handling.
   *
   * @param $db_user
   *   The database user that will be created
   * @param $db_cred
   *   The password that will be used for this user. This should be a strong
   *   password.
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

  public function createDatabase($database, $db_user, $db_cred) {
    // Drop the database
    $this->dropDatabase($dbatabase);

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