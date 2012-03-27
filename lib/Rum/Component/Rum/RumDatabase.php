<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Database\Database;
use Rum\Component\FileSystem\FileSystem;

class RumDatabase extends RumDecorator {

  private $db_server;

  private $file_system;

  const RUM_DB_MYSQL = 'MySQL';

  function __construct($rum) {
    parent::__construct($rum);
    $this->checkSetting('rum_db_type');
    $class_name = drush_get_option('rum_db_type', '');
    switch ($class_name) {
      case self::RUM_DB_MYSQL :
        $this->db_server = Database::getInstance($class_name);
        break;
      default :
        throw new RumDbServerClassNotFound($class_name);
    }
    $settings = $this->db_server->getSettings();
    $settings += array('rum_db_root_user', 'rum_db_root_pass');
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
    $db_root_user = drush_get_option('rum_db_root_user', '');
    $db_root_pass = drush_get_option('rum_db_root_pass', '');
    $this->db_server->setRootUser($db_root_user, $db_root_pass);
    $this->file_system = new FileSystem();
  }

  public function createSettingsFile() {
    if (!isset($this->db_user)) {
      throw new RumProjectDbUserNotSetException();
    }

    if (!isset($this->db_cred)) {
      throw new RumProjectDbCredNotSetException();
    }

    $project_site_folder = $this->getProjectDir() . '/www/sites/'. $this->getProjectDomain();
    $default_site_folder = $this->getProjectDir() . '/www/sites/default';
    $settings_file = $project_site_folder .'/settings.php';
    $settings_custom_file = $project_site_folder . '/settings.custom.php';

    // Adjust settings.php to run the site.
    if (!$this->file_system->checkDir($project_site_folder)) {
      $this->file_system->createDir($project_site_folder);
    }

    if (!$this->file_system->checkFile($settings_file)) {
      $settings_orig = $default_site_folder . '/default.settings.php';
      if (is_file($settings_orig)) {
        drush_shell_exec("cp $settings_orig $settings_file");
      }
      elseif (!$this->file_system->checkFile($this->getProjectDir() . "/www/misc/drupal.js")) {
        drush_log("No site available yet.", 'warning');
        return;
      }
      else {
        drush_log("Cannot stat settings.php file or default_settings.php to create one at $settings_orig.", 'error');
        return;
      }
    }

    //include_once($settings_file);
    $contents = file_get_contents($settings_file);
    $contents .= 'require "settings.custom.php";';
    $this->file_system->createFile($settings_file, $contents);

    // @todo MySQL specific logic here. We need to abstract and hide this from
    // this class.
    $core_version = $this->getCoreVersion();
    switch ($core_version) {
      case RUM_CORE_VERSION_6 :
         $db_link = '$db_url = "mysql://' . $this->db_user .':' . $this->db_cred . '@localhost/' . $this->db_user . '";';
        break;
      case RUM_CORE_VERSION_7 :
         $db_link = "\$databases['default']['default'] = array(
        'driver' => 'mysql',
        'database' => '". $this->db_user ."',
        'username' => '". $this->db_user ."',
        'password' => '". $this->db_cred ."',
        'host' => 'localhost',
        'prefix' => '',
        'collation' => 'utf8_general_ci',
     );";
        break;
    }

    $base = $this->getBaseSettingsFileContents();
    $contents = $base . $db_link;
    $this->file_system->createFile($settings_custom_file, $contents);
  }
  
  private function getBaseSettingsFileContents() {
    $output = <<<SETTINGS
<?php

\$update_free_access = FALSE;
ini_set("arg_separator.output",     "&amp;");
ini_set("magic_quotes_runtime",     0);
ini_set("magic_quotes_sybase",      0);
ini_set("session.cache_expire",     200000);
ini_set("session.cache_limiter",    "none");
ini_set("session.cookie_lifetime",  2000000);
ini_set("session.gc_maxlifetime",   200000);
ini_set("session.save_handler",     "user");
ini_set("session.use_cookies",      1);
ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid",    0);
ini_set("url_rewriter.tags",        "");
\$db_prefix = "";

SETTINGS;

    return $output;
  }

  public function setProjectDbUser($db_user, $db_cred) {
    $this->db_user = $db_user;
    $this->db_cred = $db_cred;
  }

  public function setProjectDb($database) {
    $this->database = $database;
  }

  public function createUser() {
    $this->db_user = substr($this->getEnvironment() . '_' . strtoupper($this->getProjectName()), 0, 16);
    $this->db_cred = md5(strrev($db_user));
    $this->db_server->createUser($this->db_user, $this->db_cred);
  }

  public function createDatabase() {
    $this->db_server->createDatabase($this->database);
  }

  public function dropUser() {
    $this->db_server->dropUser($this->db_user);
  }

  public function dropDatabase() {
    $this->db_server->dropDatabase($this->database);
  }

}