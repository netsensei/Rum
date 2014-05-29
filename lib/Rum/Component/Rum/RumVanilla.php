<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;

class RumVanilla extends RumDecorator {

  private $rum;

  public function __construct($rum) {
    parent::__construct($rum);
  }

  public function downloadCore() {
    $www_dir = $this->getProjectDir() . '/' . $this->getDocumentRoot();

    if (!is_file($www_dir . '/misc/drupal.js')) {
      $core_version = $this->getCoreVersion();
      $core = "drupal-" . $core_version . ".x";
      drush_log(dt('Downloading Drupal core...'), 'status');
      drush_set_option('backend', TRUE);
      drush_set_option('destination', $this->getProjectDir());
      drush_set_option('drupal-project-rename', $this->getDocumentRoot());
      if (drush_invoke('pm-download', array($core)) === FALSE) {
        return drush_set_error('', 'Drupal core download/extract failed.');
      }
      drush_set_option('backend', FALSE);
    } else {
      drush_log(dt('Drupal already downloaded and unpacked for this project.'));
    }
  }

}
