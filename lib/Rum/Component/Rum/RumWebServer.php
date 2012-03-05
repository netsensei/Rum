<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\WebServer\WebServer;
use Rum\Component\WebServer\Exception\RumWebServerClassNotFound;

class RumWebServer extends RumDecorator {

  private $web_server;

  private $file_system;

  const RUM_HTTP_APACHE = 'Apache';

  const RUM_HTTP_NGINX = 'Nginx';

  function __construct($rum) {
    parent::__construct($rum);
    $this->checkSetting('rum_http_type');
    $class_name = drush_get_option('rum_http_type', '');
    switch ($class_name) {
      case self::RUM_HTTP_APACHE :
      case self::RUM_HTTP_NGINX :
        $this->web_server = WebServer::getInstance($class_name);
        break;
      default :
        throw new RumWebServerClassNotFound($class_name);
    }
    $this->web_server = WebServer::getInstance('Apache');
    $settings = array('rum_http_port');
    $settings += $this->web_server->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  public function createVhost() {
    $port = drush_get_option('rum_http_port', '');
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