<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\FileSystem\FileSystem;

class RumSettingsFile extends RumDecorator {

  public function __construct($rum) {
    parent::__construct($rum);
    $this->file_system = new FileSystem();
  }

  public function createSettingsFile($database, $db_user, $db_cred) {    
    $project_site_folder = $this->getProjectDir() . '/' . $this->getDocumentRoot() . '/sites/'. $this->getProjectDomain();
    $default_site_folder = $this->getProjectDir() . '/' . $this->getDocumentRoot() . '/sites/default';
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
      elseif (!$this->file_system->checkFile($this->getProjectDir() . '/' . $this->getDocumentRoot() . '/misc/drupal.js')) {
        drush_log("No site available yet.", 'warning');
        return;
      }
      else {
        drush_log("Cannot stat settings.php file or default_settings.php to create one at $settings_orig.", 'error');
        return;
      }
    }

    $contents = file_get_contents($settings_file);
    $contents .= 'require "settings.custom.php";';
    $this->file_system->createFile($settings_file, $contents);

    // @todo MySQL specific logic here. We need to abstract and hide this from
    // this class.
    $core_version = $this->getCoreVersion();
    switch ($core_version) {
      case RUM_CORE_VERSION_6 :
         $db_link = '$db_url = "mysql://' . $db_user .':' . $db_cred . '@localhost/' . $database . '";';
        break;
      case RUM_CORE_VERSION_7 :
         $db_link = "\$databases['default']['default'] = array(
        'driver' => 'mysql',
        'database' => '". $database ."',
        'username' => '". $db_user ."',
        'password' => '". $db_cred ."',
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
  
}