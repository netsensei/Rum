<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;

class RumVanilla extends RumDecorator {
  
  private $rum;
  
  public function __construct($rum) {
    parent::__construct($rum);
  }

  public function downloadCore($version) {
    $www_dir = $this->project_dir . '/www';
    drush_set_option('destination', $this->project_dir);
    drush_set_option('drupal-project-rename', basename(www_dir));
    drush_pm_download('drupal-' . $version);
    if (drush_get_error()) return FALSE; // Early exit if we see an error.;
  }

}