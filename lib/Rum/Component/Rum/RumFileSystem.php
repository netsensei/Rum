<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\FileSystem\FileSystem;

class RumFileSystem extends RumDecorator {

  private $file_system;

  private $project_dir;

  public function __construct($rum) {
    parent::__construct($rum);
    $this->file_system = new FileSystem();
  }

  public function createWorkSpace() {
    $workspace = $this->getWorkspace();
    $this->createDirectory($workspace);
  }

  public function createProjectDir($scaffold = TRUE) {
    $project_dir = $this->getProjectDir();
    $this->createDirectory($project_dir);
    $this->project_dir = $project_dir;
    if ($scaffold) {
      $project_db_dir = $project_dir . '/db';
      $this->createDirectory($project_db_dir);
      $project_web_dir = $project_dir . '/' . $this->getDocumentRoot();
      $this->createDirectory($project_web_dir);
    }
  }

  public function isActiveProjectDir() {
  	$contents = drush_scan_directory($this->getProjectDir(), '/.*/', array('.', '..'), 0, FALSE, 'basename', 0, FALSE);
    if (!empty($contents)) {
      return TRUE;
    }
    
    return FALSE;
  }

  public function removeProjectDir() {
    $project_dir = $this->getProjectDir();
    $this->removeDirectory($project_dir);
  }

  private function createDirectory($directory) {
    $result = $this->file_system->checkDir($directory);

    if (!$result) {
      $create = drush_confirm(dt('Directory %directory does not exist, do you want me to create it?', array('%directory' => $directory)));
      if ($create) {
        $result = $this->file_system->createDir($directory);
      } else {
        $result = drush_user_abort('Aborting...');
      }
    }

    return $result;
  }

  private function removeDirectory($directory) {
    $result = $this->file_system->checkDir($directory);
    if ($result) {
      $remove = drush_confirm(dt('Do you want to delete directory %directory?', array('%directory' => $directory)));
      if ($remove) {
        $result = $this->file_system->removeDir($directory);
      } else {
        $result = drush_user_abort('Aborting...');
      }
    }

    return $result;
  }

}