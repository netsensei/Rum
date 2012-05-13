<?php

use Rum\Component\Rum\Rum;
use Rum\Component\Rum\RumDatabase;
use Rum\Component\Rum\RumSettingsFile;
use Rum\Component\Rum\RumFileSystem;
use Rum\Component\Rum\RumVanilla;
use Rum\Component\Rum\RumInstaller;
use Rum\Component\Rum\RumWebServer;
use Rum\Component\Rum\RumDrush;
use Rum\Component\Rum\RumHosts;

function drush_rum_create($type = NULL, $project_name = NULL) {
define('RUM_CORE_VERSION_6', 6);
define('RUM_CORE_VERSION_7', 7);

  if (!$type) {
    drush_set_error(dt('You did not provide an installation type.'));
    return FALSE;
  }

  if (!$project_name) {
    drush_set_error(dt('You did not provide a project name.'));
    return FALSE;
  }
  
  $project_dir = drush_get_option('project-dir', '');
  if (empty($project_dir)) {
    // When there is no project_dir, use project_name as project_dir
    // will be sanitized.
    $project_dir = $project_name;
  }

  try {
    // Init
    $rum = new Rum($project_name, $project_dir);

    // Environment
    $environment = drush_choice(array('DEV', 'QA', 'PROD'), dt('Choose your environment.'));
    $rum->setEnviroment($environment);

    // Create the folders in the workspace
    $rum = new RumFileSystem($rum);
    $rum->createWorkSpace();
    $rum->createProjectDir();

    // Download Drupal core
    $rum = new RumVanilla($rum);
    $options = array('Drupal 6.x', 'Drupal 7.x');
    $versions = array(RUM_CORE_VERSION_6, RUM_CORE_VERSION_7);
    $choice = drush_choice($options, dt('What Core version do you want to download?'));
    $rum->setCoreVersion($versions[$choice]);
    $rum->downloadCore();

    // Add a new entry in the hosts file (if we're run in DEV)
    $rum = new RumHosts($rum);
    $rum->addHostsEntry();

    // Create a new virtual host
    $rum = new RumWebServer($rum);
    $rum->createVhost();

    // Restart webserver
    $rum->restart();

    // Create a new drushrc file
    $rum = new RumDrush($rum);
    $rum->createDrush();

    // Bootstrap Drupal to the ROOT phase
    $rum->bootstrap(DRUSH_BOOTSTRAP_DRUPAL_ROOT);

    // Create the Database
    $rum = new RumDatabase($rum);
    $rum->setProjectDbUser();
    $rum->createUser();
    $rum->setProjectDb();
    $rum->createDatabase();

    $database = $rum->getProjectDb();
    $password = $rum->getProjectDbCred();
    $user = $rum->getProjectDbUser();

    // Create a settings file
    $rum = new RumSettingsFile($rum);
    $rum->createSettingsFile($database, $user, $password);

    // Finally, install the site
    $install = drush_confirm(dt('Do you want to install the site now?'));
    if ($install) {
      $profile = NULL;
      $rum = new RumInstaller($rum);
      $rum->install($profile);
    }
  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }
}