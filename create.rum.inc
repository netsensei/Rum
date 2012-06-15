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
use Rum\Component\Rum\RumState;

define('RUM_ORIGIN_VANILLA', 'vanilla');
define('RUM_ORIGIN_REPOSITORY', 'repository');
define('RUM_CORE_VERSION_6', 6);
define('RUM_CORE_VERSION_7', 7);

function drush_rum_create($origin = NULL, $project_name = NULL) {

  if (!$origin) {
    drush_set_error(dt('You did not provide an installation origin.'));
    return FALSE;
  }

  if (!$project_name) {
    drush_set_error(dt('You did not provide a project name.'));
    return FALSE;
  }
  
  switch ($origin) {
    case RUM_ORIGIN_VANILLA :
      $result = rum_create_vanilla($project_name);
      break;
    case RUM_ORIGIN_REPOSITORY :
      $result = rum_create_repository($project_name);
      break;
  }

  return $result;
}

function rum_create_vanilla($project_name = NULL) {
  $project_dir = drush_get_option('project-dir', '');
  if (empty($project_dir)) {
    // When there is no project_dir, use project_name as project_dir
    // will be sanitized.
    $project_dir = $project_name;
  }

  try {
    // Init
    $rum = new Rum($project_name, $project_dir);

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

  return TRUE;
}

function rum_create_repository($project_name = NULL) {
  $project_dir = drush_get_option('project-dir', '');
  if (empty($project_dir)) {
    // When there is no project_dir opiton set, use project_name as project_dir
    // will be sanitized.
    $project_dir = $project_name;
  }

  try {
    // Init
    $rum = new Rum($project_name, $project_dir);

    // Create the folders in the workspace
    $rum = new RumFileSystem($rum);
    $rum->createWorkSpace();
    $rum->createProjectDir(FALSE);

    // Fetch from a repository
    $options = array('Git', 'Subversion');
    $choice = drush_choice($options, dt('Choose the type of repository software.'));
    $rum = new RumState($rum, $options[$choice]);

    $repository = drush_prompt(dt("Enter the url of the projects' repository"), '');
    $rum->fetch($repository, $rum->getProjectDir());

    // Create an ignore file (ignores files from the repo)
    $create_ignore = drush_confirm(dt('Do you want to create an ignore file?'));
    if ($create_ignore) {
      $rum->createIgnoreFile($rum->getProjectDir());
    }

    $contents = drush_scan_directory($rum->getProjectDir(), '/.*/', array('.', '..'), 0, FALSE, 'basename', 0, FALSE);
    $options = array_keys($contents);
    $choice = drush_choice($options, dt('Set the DocumentRoot of your project.'));
    $rum->setDocumentRoot($options[$choice]);

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

    // Import a database from the db/ folder
    $import = drush_confirm(dt('Do you want to import a database dump right now?'));
    if ($import) {
      $db_dir = $rum->getProjectDir() . '/' . $rum->getDatabaseDir();
      $contents = drush_scan_directory($db_dir, '/.*/', array('.', '..'), 0, FALSE, 'basename', 0, FALSE);
      $options = array_keys($contents);
      $choice = drush_choice($options, dt('Select the database dump you want to import.'));
      $rum->bootstrap(DRUSH_BOOTSTRAP_DRUPAL_CONFIGURATION);
      $base_name = $options[$choice];
      $contents[$base_name]->filename;
      if (isset($contents[$base_name]->filename)) {
       $exec = drush_sql_build_exec(NULL, $contents[$base_name]->filename);
       if (drush_shell_exec($exec)) {
         drush_log(dt('Database dump succesfully imported'), 'success');
       }
      }
    }
  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }

  return TRUE;
}