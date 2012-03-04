<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\Rum;

abstract class RumDecorator implements RumInterface {

  private $rum;

  public function __construct(Rum $rum) {
    $this->rum = $rum;
  }

  public function getWorkspace() {
    return $this->rum->getWorkspace();
  }

  public function getProjectDomain() {
    return $this->rum->getProjectDomain();
  }
  
  public function getProjectDir() {
    return $this->rum->getProjectDir();
  }

  public function getProjectName() {
    return $this->rum->getProjectName();
  }

  public function getHostName() {
    return $this->rum->getHostName();
  }

  public function getOs() {
    return $this->rum->getOs();
  }

  public function getEnvironment() {
    return $this->rum->getEnvironment();
  }

  public function getTime() {
    return $this->rum->getTime();
  }

  public function checkSetting($setting) {
    return $this->rum->checkSetting($setting);
  }
}
