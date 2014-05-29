<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\FileSystem\FileSystem;
use Rum\Component\Settings\Settings;

class RumSettingsFile extends RumDecorator {

  private $settings_generator;

  public function __construct($rum) {
    parent::__construct($rum);

    $core_version = $this->getCoreVersion();

    switch ($core_version) {
      case RUM_CORE_VERSION_6 :
        $class_name = 'SettingsVersion6';
        break;
      case RUM_CORE_VERSION_7 :
        $class_name = 'SettingsVersion7';
        break;
    }

    $this->settings_generator = Settings::getInstance($class_name);
    $this->file_system = new FileSystem();
  }

  public function createSettingsFile($database, $db_user, $db_cred) {
    $web_dir = $this->getDocumentRoot();
    $project_domain = $this->getProjectDomain();
    if (empty($web_dir)) {
      $document_root = $this->getProjectDir();
    }
    else {
      $document_root = $this->getProjectDir() . '/' . $web_dir;
    }
    $project_site_folder = $document_root . '/sites/'. $project_domain;

    // Create the settings.php file
    $this->settings_generator->generate_file($database, $db_user, $db_cred, $project_domain);
    drush_log(dt('Created the settings.php file.'), 'success');

    // Do we want a multisite setup or not?
    $multi = drush_confirm(dt('Do you want to configure Drupal as a multi site setup (settings.php in a separate sites folder)?'));

    if ($multi) {
      // Create an empty project folder. conf_path(FALSE) will pick it up and take care of the rest
      if (!$this->file_system->checkDir($project_site_folder)) {
        $this->file_system->createDir($project_site_folder);
        $source = $this->getProjectDir() . '/' . $this->getDocumentRoot() . '/sites/default/settings.php';
        $target = $this->getProjectDir() . '/' . $this->getDocumentRoot() . '/sites/'. $this->getProjectDomain() . '/settings.php';
        $this->file_system->moveFile($source, $target);
      }
    }
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
