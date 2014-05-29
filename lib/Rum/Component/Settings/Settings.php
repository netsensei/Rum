<?php

namespace Rum\Component\Settings;

use Rum\Component\Settings\SettingsVersion6;
use Rum\Component\Settings\SettingsVersion7;

abstract class Settings {

  protected static $instance = NULL;

  final static public function getInstance($class_name) {
    $class_name = 'Rum\\Component\\Settings\\' . $class_name;

    if (!self::$instance) {
      self::$instance = new $class_name();
    }

    return self::$instance;
  }

  abstract public function generate_file($database, $db_user, $db_pass, $default_site_folder);

}
