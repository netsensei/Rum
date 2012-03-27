<?php

namespace Rum\Component\Rum;

use Rum\Component\FileSystem\FileSystem;
use Rum\Component\Rum\RumDecorator;
use Rum\Component\WebServer\Hosts;

class RumHosts extends RumDecorator {

  private $hosts;

  public function __construct($rum) {
    parent::__construct($rum);
    $this->hosts = Hosts::getInstance();
    $settings = $this->hosts->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  public function addHostsEntry() {
    if ($this->getEnvironment() == 'DEV') {
      $this->hosts->addHostsEntry($this->getProjectDomain());
    }
  }

  public function removeHostsEntry() {
    if ($this->getEnvironment() == 'DEV') {
      $this->hosts->removeHostsEntry($this->getProjectDomain());
    }
  }

  public function checkSetting($setting) {
    $result = parent::checkSetting($setting);

    // Extra check to make sure the hosts file *does* exist
    if ($result) {
      $this->file_system = new FileSystem();
      $hosts_file = drush_get_option('rum_hosts_file', '');
      if ($this->file_system->checkFile($hosts_file)) {
         return TRUE;
      } else {
        throw new RumHostsFileDoesNotExist($hosts_file);
      }
    }

    return FALSE;
  }
}