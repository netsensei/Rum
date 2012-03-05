<?php

namespace Rum\Component\WebServer;

use Rum\Component\FileSystem\FileSystem;
use Rum\Component\WebServer\Exception\RumHostsFileDoesNotExist;

class Hosts {

  protected static $instance = NULL;
  
  public function getInstance() {
    if (!self::$instance) {
      $class_name = __CLASS__;
      self::$instance = new $class_name;
    }

    return self::$instance;
  }

  public function addHostsEntry($project_domain) {
    $hosts_file = drush_get_option('rum_hosts_file', '');
    drush_log(dt('Adding host entry to %file', array('%file' => $hosts_file)), 'status');
    $hosts_lines = explode("\n", file_get_contents($hosts_file));
    $host_available = FALSE;
    foreach ($hosts_lines as $line) {
      if (preg_match("/" . $project_domain . "/", $line)) {
        $host_available = TRUE;
      }
    }

    if (!$host_available) {
      $hosts_lines[] = "127.0.0.1\t" . $project_domain;
      // Use exec because the lines might contain % which we really do not need here.
      exec("sudo sh -c 'echo \"" . implode("\n", $hosts_lines) . "\" > /etc/hosts'");
      drush_log('Entry "'. $project_domain .'" added to hosts file', 'success');
      drush_log(dt('Entry %project_domain added to hosts file', array('%project_domain' => $project_domain)), 'success');
    } else {
      drush_log(dt('Entry %project_domain already in hosts file', array('%project_domain' => $project_domain)), 'warning');
    }
  }

  public function getSettings() {
    return array('rum_hosts_file');
  }
}