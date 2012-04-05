<?php

use Rum\Component\Rum\Rum;
use Rum\Component\Rum\RumDatabase;
use Rum\Component\Rum\RumFileSystem;
use Rum\Component\Rum\RumVanilla;
use Rum\Component\Rum\RumWebServer;
use Rum\Component\Rum\RumDrush;
use Rum\Component\Rum\RumHosts;

function drush_rum_create($type, $project_name = NULL) {
  // Set the project name
  if (is_null($project_name)) {
    $project_name = drush_prompt(dt('Enter the project name'));
  }

  $project_dir = drush_prompt(dt('Enter the project directory'));

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
    $msg = dt('What Core version do you want to download?');
    $version = drush_choice(array('Version 6', 'Version 7'), $msg);
    $rum->setCoreVersion($version);
    $rum->downloadCore();

    // Add a new entry in the hosts file (if we're run in DEV)
    $rum = new RumHosts($rum);
    $rum->addHostsEntry();

    // Create a new virtual host
    $rum = new RumWebServer($rum);
    $rum->createVhost();

    // Restart webserver
    $rum->restart();

    // Create a new database user
    $rum = new RumDatabase($rum);
    $rum->createUser();
    $rum->createSettingsFile();

    // Create a new drushrc file
    $rum = new RumDrush($rum);
    $rum->createDrush();

    // Finally, install the site
    // @todo Do we put this in a separate class? Might wanna reuse for a profile install...
    $install = drush_confirm(dt('Do you want to install the site now?'));
    if ($install) {
      // The project name is the alias name of our site
      $alias = '@' . $rum->getProjectName();
      $site_record = drush_sitealias_get_record($alias);
      drush_bootstrap_max_to_sitealias($site_record, DRUSH_BOOTSTRAP_DRUPAL_CONFIGURATION);
      // Make this configurable? Provide defaults!
      $options['uid1_account'] = drush_prompt(dt('Enter the administrator (uid=1) account name'));
      $options['uid1_pass'] = drush_prompt(dt('Enter the administrator (uid=1) password'));
      $options['uid1_mail'] = drush_prompt(dt('Enter the administrator (uid=1) e-mail address'));
      $options['locale'] = drush_prompt(dt('Enter your desired locale'));
      $options['site_name'] = drush_prompt(dt('Enter the name of your site'));
      $options['site_mail'] = drush_prompt(dt('Enter the global mail address of your site'));
      drush_include_engine('drupal', 'site_install', drush_drupal_major_version());
      drush_core_site_install_version(NULL, $options);
    }

  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }
}