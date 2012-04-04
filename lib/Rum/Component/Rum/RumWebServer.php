<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\WebServer\WebServer;
use Rum\Component\FileSystem\FileSystem;
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
    $this->file_system = new FileSystem();
    switch ($class_name) {
      case self::RUM_HTTP_APACHE :
      case self::RUM_HTTP_NGINX :
        $this->web_server = WebServer::getInstance($class_name);
        break;
      default :
        throw new RumWebServerClassNotFound($class_name);
    }
    $settings = array('rum_http_port', 'rum_http_doc_root');
    $settings += $this->web_server->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  public function createVhost() {
    $port = drush_get_option('rum_http_port', '');
    $project_domain = $this->getProjectDomain();
    $web_dir = $this->getProjectDir() . '/www';
    $link = drush_get_option('rum_http_doc_root', '') . '/' . $this->getProjectName();
    $time = $this->getTime();
    if (!$this->file_system->checkFile($link)) {
      $this->file_system->createLink($web_dir, $link);
    }
    $this->web_server->createVhost($time, $port, $project_domain, $link);
  }

  public function removeVhost() {
    $link = drush_get_option('rum_http_doc_root', '') . '/' . $this->getProjectName();
    if ($this->file_system->checkFile($link)) {
      $this->file_system->removeFile($link);
    }
    $project_domain = $this->getProjectDomain();
    $this->web_server->removeVhost($project_domain);
  }

  public function restart() {
    $os = $this->getOs();
    $this->web_server->restart($os);
  }
}