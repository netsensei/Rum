<?php

namespace Rum\Component\WebServer;

use Rum\Component\WebServer\WebServer;
use Rum\Component\FileSystem\FileSystem;

class Apache extends WebServer {

  private $protection = '';

  public function __construct() {
    $this->hosts_dir = drush_get_option('rum_apache_hosts_dir', '');
    $this->log_dir = drush_get_option('rum_apache_log_dir', '');
    $this->file_system = new FileSystem();
  }

  public function getSettings() {
    return array('rum_apache_hosts_dir', 'rum_apache_log_dir');
  }

  public function createVhost($date, $port, $project_domain, $web_dir) {
    $vhost_file = $this->hosts_dir . '/' . $project_domain . '.conf';

    $contents = <<<CONFIG
    # --------------- Added by tslag_add_subdomain - $date
    <VirtualHost *:$port>
      ServerName $project_domain
      DocumentRoot $web_dir
      LogLevel info
      ErrorLog $log_dir/$project_domain/$project_domain.error.log
      CustomLog $log_dir/$project_domain/$project_domain.access.log combined

      <Directory $web_dir>
        Options Indexes FollowSymlinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
      </Directory>
    </VirtualHost>
CONFIG;

    $this->file_system->createFile($vhost_file, $contents);
  }

  public function removeVhost($project_domain) { 
    
    $vhost_file = $this->hosts_dir . '/' . $project_domain . '.conf';
    $this->file_system->removeFile($vhost_file);
    $log_dir = $this->log_dir . '/' . $project_domain;
    $this->file_system->removeFile($log_dir);
  }

  public function restart() { }

  public function setProtection() {
    $protect = drush_confirm("Do you want to protect this website via .htaccess ? This will create a htpasswd file in /etc/htpasswd.");
    if ($protect) {  
    }
  }
}
