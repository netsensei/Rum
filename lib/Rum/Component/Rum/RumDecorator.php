<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\Rum;

abstract class RumDecorator implements RumInterface {

  private $rum;

  public function __construct(RumInterface $rum) {
    $this->rum = $rum;
  }

  public function getWorkspace() {
    return $this->rum->getWorkspace();
  }

  public function getProjectDomain() {
    return $this->rum->getProjectDomain();
  }

  public function getProjectUrl() {
  	return $this->rum->getProjectUrl();
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

  public function getCoreVersion() {
    return $this->rum->getCoreVersion();
  }

  public function setCoreVersion($version) {
    $this->rum->setCoreVersion($version);
  }

  public function checkSetting($setting) {
    return $this->rum->checkSetting($setting);
  }

  public function bootstrap($phase) {
    return $this->rum->bootstrap($phase);
  }

  public function getDocumentRoot() {
    return $this->rum->getDocumentRoot();
  }

  public function setDocumentRoot($project_docroot) {
    $this->rum->setDocumentRoot($project_docroot);
  }

  public function getDatabaseDir() {
    return $this->rum->getDatabaseDir();
  }

  public function setDatabaseDir($project_db_dir) {
    $this->rum->setDatabaseDir($project_db_dir);
  }
}
