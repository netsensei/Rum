<?php

namespace Rum\Component\Settings;

use Rum\Component\Settings\Settings;
use Rum\Component\FileSystem\FileSystem;

class SettingsVersion7 extends Settings {

  public function generate_file($database, $db_user, $db_pass, $project_domain) {

    // Generate settings for the database
    $settings['databases'] = array(
      'value' => array(
        'default' => array(
          'default' => array(
            'prefix' => '', // @todo make htis configurable!
            'host' => 'localhost', // @todo make this configurable!
            'driver' => 'mysql', // @todo this should depend on the relevant driver
            'database' => $database,
            'username' => $db_user,
            'password' => $db_pass,
          ),
        ),
      ),
      'required' => TRUE,
    );

    try {
      require_once DRUPAL_ROOT . '/includes/install.inc';
      $variables['url'] = "http://" . $project_domain . '/index.php';
      // Override variables since this is obviously run from CLI
      // We do this to confuse the cat... err. conf_path()
      drupal_override_server_variables($variables);
      // Rewrite the settings file in its' correct place
      drupal_rewrite_settings($settings);
    } catch (Exception $e) {
      throw new RumCouldNotCreateSettingsFileException($e->getMessage());
    }

    return TRUE;
  }

}
