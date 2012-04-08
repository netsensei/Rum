<?php
/**
 * @file
 * Database support for the Rum API
 *
 * Contains abstraction for the Rum API from different concrete implementations
 * of database drivers.Since Drupal relies on a database system to store content
 * and configuration, this part of the API manages the (de)installation of a
 * database for a project.
 */

namespace Rum\Component\Database;

/**
 * Base Database API class
 *
 * Every database driver should provide a concrete implementation of this class.
 * A database driver should do a few things
 * - Connect with your database system
 * - Create a new database user related to your project
 * - Create a new database related to your project
 * - Grant privileges to the database user for this database
 * - Drop the database
 * - Drop the database user
 */
abstract class Database {

  /**
   * The username of the root account used to log in with the database system
   *
   * @var string
   */
  protected $db_root_user;

  /**
   * The password of the root account used to log in with the database system
   *
   * @var string
   */
  protected $db_root_pass;

  protected static $instance = NULL;

  /**
   * Get an instance of type Database.
   *
   * This is a factory function which will return an object of type Database.
   * When adding your own implementation for a database language (i.e. couchDB)
   * your class should extend and implement the Database class.
   *
   * @todo
   *   Do we need this as a singleton?
   *
   * @param $class_name
   *   The class name of the object you want to instantiate. i.e. MySQL
   *
   * @return
   *   An object of type $class_name. PHP will throw an error if $class_name is
   *   not a valid class.
   */
  final public function getInstance($class_name) {
    $class_name = 'Rum\\Component\\Database\\' . $class_name;

    if (!self::$instance) {
      self::$instance = new $class_name();
    }

    return self::$instance;
  }

  /**
   * Sets the root user of the database system.
   *
   * A database system should have a root or system user which has superuser
   * privileges. Setting these allows Rum to create new databases and grant
   * privileges to their corresponding users.
   *
   * @param $db_root_user
   *   The username of the system user
   * @param $db_root_pass
   *   The password of the system user
   */
  public function setRootUser($db_root_user, $db_root_pass) {
    $this->db_root_user = $db_root_user;
    $this->db_root_pass = $db_root_pass;
  }

  /**
   * Get an array of required settings.
   *
   * The Rum API will perform a check against required settings. A specific
   * implementation might require specific variables to be set in drushrc.php
   * Implementing getSettings() assures those variables will be set. Rum will
   * perform a check whether or not these variables are set.
   *
   * @return
   *   An array of strings. Each string is a required setting which needs to be
   *   set in drushrc.php or through drush_set_option().
   */
  abstract public function getSettings();

  /**
   * Create a database and a corresponding database user.
   *
   * Automatically create a new database user and a database. The database user
   * is granted all privileges on the database. By convention, the name of the
   * user is reused as the database name.
   *
   * @param $db_user
   *   The name of the user. The database name is derived from the user name.
   * @param db_cred
   *   The password to be used for the user to log in in the database system.
   *   Make sure this is a strong password
   */
  abstract public function createUser($db_user, $db_cred);

  abstract public function dropUser($db_user);

  abstract public function createDatabase($database, $db_user, $db_cred);

  abstract public function dropDatabase($database);

}