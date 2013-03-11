<?php

namespace Rum\Component\Drush;

use Rum\Component\FileSystem\FileSystem;

class Drush {

  private $file_system;

  public function __construct() {
    $this->file_system = new FileSystem;
  }

  public function getSettings() {
    return array('rum-drush-dir');
  }

  public function createDrush($environment, $project_name, $domain_name, $project_dir, $document_root) {
    $drush_dir = drush_get_option('rum-drush-dir', '');
    $file = $drush_dir . '/'. $project_name . '.aliases.drushrc.php';
    $rum_project_dir = basename($project_dir);
    $root = (!empty($document_root)) ? $project_dir . '/' . $document_root : $project_dir;
    if ($this->file_system->checkFile($file)) {
      drush_log(dt('The drush file %file already exists', array('%file' => $file)));
    } else {
      $contents = <<<DRUSH
<?php
  \$aliases['$project_name'] = array (
    'root' => '$root',
    'uri' => '$domain_name',
    'rum_environment' => '$environment',
    'rum_project_dir' => '$rum_project_dir',
    'rum_project_name' => '$project_name',
  );
DRUSH;

      $this->file_system->createFile($file, $contents);
    }
  }

  public function removeDrush($project_name) {
    $drush_dir = drush_get_option('rum-drush-dir', '');
    $file = $drush_dir . '/'. $project_name . '.aliases.drushrc.php';
    if ($this->file_system->checkFile($file)) {
      $this->file_system->removeFile($file);
    }
  }
}