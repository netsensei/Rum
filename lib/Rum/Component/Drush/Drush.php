<?php

namespace Rum\Component\Drush;

use Rum\Component\FileSystem\FileSystem;

class Drush {

  private $file_system;

  public function __construct() {
    $this->file_system = new FileSystem;
  }
  
  public function getSettings() {
    return array('rum_drush_dir');
  }

  public function createDrush($environment, $project_name, $domain_name, $project_dir) {
    $drush_dir = drush_get_option('rum_drush_dir', '');
    $file = $drush_dir . '/'. $project_name . '.aliases.drushrc.php';
    if ($this->file_system->checkFile($file)) {
      drush_log(dt('The drush file %file already exists', array('%file' => $file)));
    } else {
      $contents = <<<DRUSH
<?php
  \$aliases['$project_name'] = array (
    'root' => '$project_dir/www',
    'uri' => '$domain_name'
  );

  \$options = array(
    'rum_environment = '$environment';
    'rum_project_dir = '$project_dir';
    'rum_project_name' = '$project_name';
  );
DRUSH;

      $this->file_system->createFile($file, $contents);
    }
  }
}