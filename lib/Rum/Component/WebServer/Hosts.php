<?php

namespace Rum\Component\WebServer;

use Rum\Component\FileSystem\FileSystem;

class Hosts {

  private static $instance;

  private $hosts_file;

  private $file_system;

  private function __construct() {
    $this->file_system = new FileSystem();
    $hosts_file = drush_get_option('rum_hosts_file', '');
    if ($this->file_system->checkFile($hosts_file)) {
      $this->hosts_file = $hosts_file; 
    } else {
      throw new RumHostsFileDoesNotExist($hosts_file);
    }
  }

  public function getInstance() {
    if (!self::$instance) {
      self::$instance =  new Hosts();
    }

    return self::$instance;
  }

  public function addHostsEntry($project_domain) {
    $hosts_lines = explode("\n", file_get_contents($this->hosts_file));
    $host_available = FALSE;
    foreach ($hosts_lines as $line) {
      if (preg_match("/" . $project_domain . "/", $line)) {
        $host_available = TRUE;
      }
    }
    if (!$host_available) {
      $hosts_lines[] = "127.0.0.1\t" . $project_domain;
    }

    // Use exec because the lines might contain % which we really do not need here.
    exec("sudo sh -c 'echo \"" . implode("\n", $hosts_lines) . "\" > /etc/hosts'");
    drush_log('Entry "'. $project_domain .'" added to hosts file', 'success');
  }

  public function getSettings() {
    return array('rum_hosts_file');
  }
}