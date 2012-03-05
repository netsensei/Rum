<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\WebServer\WebServer;

class RumWebServer extends RumDecorator {

  private $web_server;

  private $file_system;

  function __construct($rum) {
    parent::__construct($rum);
    $this->web_server = WebServer::getInstance('Apache');
    $settings = array('rum_http_port');
    $settings += $this->web_server->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  public function createVhost() {
    $port = $this->port;
    $project_domain = $this->getProjectDomain();
    $web_dir = $this->getProjectDir() . '/www';
    $time = $this->getTime();
    $this->web_server->createVhost($time, $port, $project_domain, $web_dir);
  }

  public function removeVhost() {
  }

  public function restart() {
    $os = $this->getOs();
    $this->web_server->restart($os);
  }
}