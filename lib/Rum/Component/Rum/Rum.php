<?php

namespace Rum\Component\Rum;

use Rum\Component\FileSystem\FileSystem;
use Rum\Component\Rum\Exception\RumSettingDoesNotExist;
use Rum\Component\Rum\Exception\RumNoValidCoreVersionException;
use Rum\Component\Rum\Exception\RumBootstrapDrupalConfigurationFailed;
use Rum\Component\Rum\Exception\RumNoValidEnvironmentSpaceException;
use Rum\Component\Rum\Exception\RumNoValidProjectDirException;
use Rum\Component\Rum\Exception\RumNoValidProjectNameException;
use Rum\Component\Rum\Exception\RumNoValidProjectDomainException;

class Rum implements RumInterface {

  private $workspace;

  private $host_name;

  private $project_name;

  private $project_dir;

  private $project_domain;
  
  private $project_docroot;

  private $os;

  private $environment;

  private $settings_map;

  private $date;

  private $core_version = NULL;

  public function __construct($project_name, $project_dir) {
    drush_log(dt('Initializing Rum ...'), 'status');
    // Set a few defaults in .drushrc.php. We can overrule these through the cli
    $this->settings_map = array(
      'rum-workspace', 'rum-host', 'rum-os', 'rum-environment', 'rum-docroot',
    );
    foreach ($this->settings_map as $setting) {
      $this->checkSetting($setting);
    }
    // Set the current time once.
    date_default_timezone_set(drush_get_option('rum-timezone', 'Europe/Brussels'));
    $this->date = date("Y-m-d_H-i-s", $_SERVER['REQUEST_TIME']);
    // Set the workspace
    $this->workspace = drush_get_option('rum-workspace', '');
    // Set the hostname of your machine (i.e. netsensei, stalski, swentel, atlas,...)
    $this->host_name = drush_get_option('rum-host', '');
    // Set the type of OS you're using. Rum is not OS aware.
    $this->os = drush_get_option('rum-os', '');
    // Set the environment
    $this->environment = drush_get_option('rum-environment', '');
    // Set the project name
    $this->project_name = $project_name;
    // Set the project_dir for the action on this particular project
    $project_dir = FileSystem::sanitize($project_dir);
    $this->project_dir = $this->workspace . '/' . $project_dir;
    // Generate a domain name. Will be hostname.project_name (i.e. netsensei.foobar)
    $this->project_domain = $this->host_name . '.' . $project_name;
    // Set the document root of your project i.e. 'www'
    $this->project_docroot = drush_get_option('rum-docroot', '');
  }

  public function getWorkspace() {
    return $this->workspace;
  }

  public function getProjectDomain() {
    if (empty($this->project_domain)) {
      throw new RumNoValidProjectDomainException();
    }
    return $this->project_domain;
  }

  public function getProjectDir() {
    if (empty($this->project_dir)) {
      throw new RumNoValidProjectDirException();
    }
    return $this->project_dir;
  }

  public function getProjectName() {
    if (empty($this->project_name)) {
      throw new RumNoValidProjectNameException();
    }

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
    $spaces = $this->getEnvironmentSpaces();
    if (!in_array($environment, $spaces)) {
      throw RumNoValidEnvironmentSpaceException($environment);
    }

    return $this->enviroment = $environment;
  }

  public function getEnvironmentSpaces() {
    return array('DEV', 'QA', 'PROD');
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
      case RUM_CORE_VERSION_6 :
        $this->core_version = RUM_CORE_VERSION_6;
        break;
      case RUM_CORE_VERSION_7 :
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
      if ($version = drush_drupal_major_version()) {
        return $version;
      }

      throw new RumNoValidCoreVersionException();
    }

    return $this->core_version;
  }
  
  public function setDocumentRoot($document_root) {
    $this->project_docroot = $document_root;
  }

  public function getDocumentRoot() {
    return $this->project_docroot;
  }
}