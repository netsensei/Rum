<?php

namespace Rum\Component\WebServer;

use Rum\Component\WebServer\WebServer;
use Rum\Component\FileSystem\FileSystem;

class Apache extends WebServer {

  private $protection = '';

  const RUM_APACHE_UBUNTU = 'ubuntu';

  const RUM_APACHE_OSX = 'osx';

  const RUM_APACHE_FEDORA = 'fedora';

  public function __construct() {
    $this->hosts_dir = drush_get_option('rum-apache-hosts-dir', '');
    $this->log_dir = drush_get_option('rum-apache-log-dir', '');
    $this->file_system = new FileSystem();
  }

  public function getSettings() {
    return array('rum_apache_hosts_dir', 'rum_apache_log_dir');
  }

  public function createVhost($time, $port, $project_domain, $web_dir) {
    $vhost_file = $this->hosts_dir . '/' . $project_domain . '.conf';
    $vhost_log_dir = $this->log_dir . '/' . $project_domain;

    $contents = <<<CONFIG
    # --------------- Added by Rum - $time
    <VirtualHost *:$port>
      ServerName $project_domain
      DocumentRoot $web_dir
      LogLevel info
      ErrorLog $vhost_log_dir/$project_domain.error.log
      CustomLog $vhost_log_dir/$project_domain.access.log combined

      <Directory $web_dir>
        Options Indexes FollowSymlinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
      </Directory>
    </VirtualHost>
CONFIG;

    if (!$this->file_system->checkFile($vhost_file)) {
      $this->file_system->createFile($vhost_file, $contents);
    }

    if (!$this->file_system->checkDir($vhost_log_dir)) {
      $this->file_system->createDir($vhost_log_dir);
    }
  }

  public function removeVhost($project_domain) {
    $vhost_file = $this->hosts_dir . '/' . $project_domain . '.conf';
    $this->file_system->removeFile($vhost_file);
    $log_dir = $this->log_dir . '/' . $project_domain;
    $this->file_system->removeFile($log_dir);
  }

  public function restart($os) {
    switch ($os) {
      case Apache::RUM_APACHE_UBUNTU :
        drush_shell_exec("sudo /etc/init.d/apache2 reload");
        break;
      case Apache::RUM_APACHE_OSX :
	      drush_shell_exec("/Applications/MAMP/bin/apache2/bin/apachectl restart");
        break;
      case Apache::RUM_APACHE_FEDORA :
        drush_shell_exec("sudo /etc/init.d/httpd reload");
        break;
      default:
        throw new RumApacheNoCommandNotFound();
    }
  }

  public function setProtection() {
    $protect = drush_confirm("Do you want to protect this website via .htaccess ? This will create a htpasswd file in /etc/htpasswd.");
    if ($protect) {
    }
  }
}
