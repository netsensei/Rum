<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\Exception\RumSettingDoesNotExist;

class Rum implements RumInterface {

  private $workspace;
  
  private $host_name;

  private $project_name;

  private $project_dir;
  
  private $project_domain;

  private $os;

  private $environment;
  
  private $settings_map;

  public function __construct($project_name, $project_dir) {
    drush_log(dt('Initializing Rum ...'), 'status');
    $this->settings_map = array(
      'rum_workspace', 'rum_host', 'rum_os', 'rum_environment',
    );
    foreach ($this->settings_map as $setting) {
      $this->checkSetting($setting);
    }
    $this->workspace = drush_get_option('rum_workspace', 'workspace');
    $this->host_name = drush_get_option('rum_host', 'rum');
    $this->os = drush_get_option('rum_os', 'osx');
    $this->environment = 'DEV'; // @todo configure this
    if (empty($project_name)) {
      // @todo bailout
    }
    $this->project_name = $project_name;
    if (empty($project_dir)) {
      // @todo bailout
    }
    $this->project_dir = $this->workspace . '/' . $project_dir;
    $this->project_domain = $this->host_name . '.' . $project_name;
  }

  public function getWorkspace() {
    return $this->workspace;
  }
  
  public function getProjectDomain() {
    return $this->project_domain;
  }
  
  public function getProjectDir() {
    return $this->project_dir;
  }
  
  public function getProjectName() {
    return $this->project_name;
  }
  
  public function getHostName() {
    return $this->host_name;
  }

  public function getOs() {
    return $this->os;
  }

  public function getEnvironment() {
    return $this->environment;
  }

  public function setEnviroment($environment) {
    return $this->enviroment = $environment;
  }

  public function tearDown() {
    $errors = drush_get_error_log();
    if (!empty($errors)) {
      foreach ($errors as $error) {
        drush_set_error(dt($error));
      }

      return FALSE;
    }

    return TRUE;
  }

  public function checkSetting($setting) {
    if (!drush_get_option($setting, FALSE)) {
      throw new RumSettingDoesNotExist($setting);
    }

    return TRUE;
  }
}