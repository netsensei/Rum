<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;

class RumVanilla extends RumDecorator {
  
  private $rum;
  
  public function __construct($rum) {
    parent::__construct($rum);
  }

  public function downloadCore() {
    $core_version = $this->getCoreVersion();

    if (!isset($core_version)) {
      throw new RumCoreVersionNotDetermined();
    }

    $www_dir = $this->getProjectDir() . '/www';

    if (!is_file($www_dir . '/misc/drupal.js')) {
      drush_set_option('destination', $this->getProjectDir());
      drush_set_option('drupal-project-rename', basename($www_dir));
      drush_pm_download('drupal-' . $core_version);
      if (drush_get_error()) return FALSE; // Early exit if we see an error.;
    } else {
      drush_log(dt('Drupal already downloaded and unpacked for this project.'));
    }
  }

}