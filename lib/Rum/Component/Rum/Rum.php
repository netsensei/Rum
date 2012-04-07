<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\Exception\RumSettingDoesNotExist;

define('RUM_CORE_VERSION_6', 6);

define('RUM_CORE_VERSION_7', 7);

class Rum implements RumInterface {

  private $workspace;

  private $host_name;

  private $project_name;

  private $project_dir;

  private $project_domain;

  private $os;

  private $environment;

  private $settings_map;

  private $date;

  private $core_version = NULL;

  public function __construct($project_name, $project_dir) {
    drush_log(dt('Initializing Rum ...'), 'status');
    $this->settings_map = array(
      'rum_workspace', 'rum_host', 'rum_os', 'rum_environment',
    );
    foreach ($this->settings_map as $setting) {
      $this->checkSetting($setting);
    }
    // Set the current time once.
    date_default_timezone_set(drush_get_option('rum_timezone', 'Europe/Brussels'));
    $this->date = date("Y-m-d_H-i-s", $_SERVER['REQUEST_TIME']);
    // Set the workspace
    $this->workspace = drush_get_option('rum_workspace', 'workspace');
    // Set the hostname of your machine (i.e. netsensei, stalski, swentel, atlas,...)
    $this->host_name = drush_get_option('rum_host', 'rum');
    // Set the type of OS you're using. Rum is not OS aware.
    $this->os = drush_get_option('rum_os', 'osx');
    // Set the environment
    $this->environment = 'DEV'; // @todo configure this
    if (empty($project_name)) {
      // @todo bailout
    }
    $this->project_name = $project_name;
    if (empty($project_dir)) {
      // @todo bailout
    }
    // Set the project_dir for the action on this particular project
    $this->project_dir = $this->workspace . '/' . $project_dir;
    // Generate a domain name. Will be hostname.project_name (i.e. netsensei.foobar)
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

  public function getTime() {
    return $this->date;
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

  public function setCoreVersion($version) {
    switch ($version) {
      case 0 :
        $this->core_version = RUM_CORE_VERSION_6;
        break;
      case 1 :
        $this->core_version = RUM_CORE_VERSION_7;
        break;
      default :
        throw new RumNoValidCoreVersionException;
    }
  }

  public function bootstrap($phase) {
    // The project name is the alias name of our site
    $project_name = $this->getProjectName();
    $alias = '@' . $project_name;
    $site_record = drush_sitealias_get_record($alias);
    if (!drush_bootstrap_max_to_sitealias($site_record, $phase)) {
      throw new RumBootstrapDrupalConfigurationFailed();
    }
  }

  public function getCoreVersion() {

    if (is_null($this->core_version)) {
      return drush_drupal_major_version();
    }

    return $this->core_version;
  }
}