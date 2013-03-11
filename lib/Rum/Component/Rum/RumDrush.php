<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Drush\Drush;

class RumDrush extends RumDecorator {

  protected $drush;

  public function __construct($rum) {
    parent::__construct($rum);
    $this->drush = new Drush();
    $settings = $this->drush->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  /**
   * @todo contains a bug: getProjectDir contains absolute path, this poses
   *  a problem when we use commands, like rd, which uses aliases since the
   *  workspace is prepended againt o the project dir.
   */
  public function createDrush() {
    $environment = $this->getEnvironment();
    $domain_name = $this->getProjectDomain();
    $project_dir = $this->getProjectDir();
    $project_name = $this->getProjectName();
    $document_root = $this->getDocumentRoot();
    $this->drush->createDrush($environment, $project_name, $domain_name, $project_dir, $document_root);
  }

  public function removeDrush() {
    $project_name = $this->getProjectName();
    $this->drush->removeDrush($project_name);
  }
}