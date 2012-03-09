<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Database\Database;

class RumDatabase extends RumDecorator {

  private $db_server;

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

    // Adjust settings.php to run the site.
    if (!file_exists($site_folder)) {
      drush_shell_exec('mkdir '. $project_site_folder);
    }

    if (!is_file($settings_file)) {
      $settings_orig = $default_site_folder . '/default.settings.php';
      if (is_file($settings_orig)) {
        drush_shell_exec("cp $settings_orig $settings_file");
      }
      elseif (!is_file($this->getProject() . "/www/misc/drupal.js")) {
        drush_log("No site available yet.", 'warning');
        return;
      }
      else {
        drush_log("Cannot stat settings.php file or default_settings.php to create one at $settings_orig.", 'error');
        return;
      }
    }
    
    $contents = $this->getBaseSettingsFileContents();
    
    

  }
  
  private function getBaseSettingsFileContents() {
    $output = <<<SETTINGS
<?php

$update_free_access = FALSE;
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
$db_prefix = "";

SETTINGS;

    return $output;
  }

  public function setProjectDbUser($db_user) {
    $this->db_user = $db_user;
  }
  
  public function setProjectDbCred($db_cred) {
    $this->db_cred = $db_cred;
  }
  
  public function createUser() {
    $this->db_user = substr($this->getEnvironment() . '_' . strtoupper($this->getProjectName()), 0, 16);
    $this->db_cred = md5(strrev($db_user));
    $this->db_server->createUser($this->db_user, $this->db_cred);
  }

  public function createDatabase() {
    
  }
  
  public function dropUser() {
    
  }
  
  public function dropDatabase() {

  }
}