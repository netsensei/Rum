<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\WebServer\Hosts;

class RumHosts extends RumDecorator {

  private $hosts;

  function __construct($rum) {
    parent::__construct($rum);
    $this->hosts = Hosts::getInstance();

    $settings = $this->hosts->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  function addHostsEntry() {
    if ($this->getEnvironment() == 'DEV') {
      $this->hosts->addHostsEntry($this->getProjectDomain());
    }
  }

  function removeHostsEntry() { 
  }
}