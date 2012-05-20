<?php

namespace Rum\Component\WebServer;

use Rum\Component\WebServer\Apache;

abstract class WebServer {

  protected static $instance = NULL;

  protected $host_name;

  protected $log_dir;

  protected $web_dir;

  protected $port;

  final public function getInstance($class_name) {
    $class_name = 'Rum\\Component\\WebServer\\' . $class_name;

    if (!self::$instance) {
      self::$instance = new $class_name();
    }

    return self::$instance;
  }

  abstract public function createVhost($date, $port, $project_domain, $web_dir);

  abstract public function removeVhost($project_domain);

  abstract public function restart($os);

  abstract public function getSettings();
}