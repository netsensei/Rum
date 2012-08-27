<?php

namespace Rum\Component\Settings;

use Rum\Component\Settings\Settings;
use Rum\Component\FileSystem\FileSystem;

class SettingsVersion6 extends Settings {

  public function generate_file($database, $db_user, $db_pass, $project_domain) {
    $value = "mysql://" . $db_user . ":" . $db_pass . "@localhost/" . $database;
    $settings['db_url'] = array(
      'value' => $value,
    );

    try {
      require_once DRUPAL_ROOT . '/includes/install.inc';
      $variables['url'] = "http://" . $project_domain . '/index.php';
      // Override variables since this is obviously run from CLI
      // We do this to confuse the cat... err. conf_path()
      $this->drupal_override_server_variables($variables);
      // Rewrite the settings file in its' correct place
      drupal_rewrite_settings($settings);
    } catch (Exception $e) {
      throw new RumCouldNotCreateSettingsFileException($e->getMessage()); 
    }

    return TRUE;
  }

  /**
   * @todo
   *  
   * D6 doesn't have this function, so we backported it here.
   */
  private function drupal_override_server_variables($variables = array()) {
    // Allow the provided URL to override any existing values in $_SERVER.
    if (isset($variables['url'])) {
      $url = parse_url($variables['url']);
      if (isset($url['host'])) {
        $_SERVER['HTTP_HOST'] = $url['host'];
      }
      if (isset($url['path'])) {
        $_SERVER['SCRIPT_NAME'] = $url['path'];
      }
      unset($variables['url']);
    }
    // Define default values for $_SERVER keys. These will be used if $_SERVER
    // does not already define them and no other values are passed in to this
    // function.
    $defaults = array(
        'HTTP_HOST' => 'localhost',
        'SCRIPT_NAME' => NULL,
        'REMOTE_ADDR' => '127.0.0.1',
        'REQUEST_METHOD' => 'GET',
        'SERVER_NAME' => NULL,
        'SERVER_SOFTWARE' => NULL,
        'HTTP_USER_AGENT' => NULL,
    );
    // Replace elements of the $_SERVER array, as appropriate.
    $_SERVER = $variables + $_SERVER + $defaults;
  }
  

}