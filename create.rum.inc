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
define('RUM_ORIGIN_SKELETON', 'skeleton');
define('RUM_CORE_VERSION_6', 6);
define('RUM_CORE_VERSION_7', 7);

/**
 * Drush command callback
 *
 * This is the drush command callback for drush rum-create. It depending
 * on the installation type, it will create a project environment and setup a
 * Drupal installation. There are two types of installation:
 *
 *   - Vanilla: Download Drupal core
 *   - Repository: Get a Drupal project from a repository i.e. Git
 *
 * If all went well, you should have an operational Drupal installation after
 * issuing this command.
 */
function drush_rum_create($origin = NULL, $project_name = NULL) {

  if (count(drush_get_arguments()) > 3) {
    drush_log(dt('You provided too many arguments.'), 'error');
    return FALSE;
  }

  if (!$origin) {
    drush_set_error(dt('You did not provide an installation origin.'));
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

  switch ($origin) {
    case RUM_ORIGIN_SKELETON :
      $result = rum_create_skeleton($project_name, $project_dir);
      break;
    case RUM_ORIGIN_VANILLA :
      $result = rum_create_vanilla($project_name, $project_dir);
      break;
    case RUM_ORIGIN_REPOSITORY :
      $result = rum_create_repository($project_name, $project_dir);
      break;
  }

  return $result;
}

function rum_create_skeleton($project_name, $project_dir) {
  try {
    // Init
    $rum = new Rum($project_name, $project_dir);

    // Create the folders in the workspace
    _rum_create_workspace_project($rum);

    // Create a new host
    $host_config = drush_confirm(dt('Do you want to add and configure a new host?'));
    if ($host_config) {
      _rum_create_host($rum);
    }

    // Create the Database
    $db_config = drush_confirm(dt('Do you want to add and configure a new database?'));
    if ($db_config) {
      $args = _rum_create_database($rum);
      foreach ($args as $key => $arg) {
        $args['!' . $key] = $arg;
      }
      drush_log(dt('Rum created a database called !database. You can access it with user:!user pass:!password', $args), 'success');
    }

    $url = $rum->getProjectUrl();
    drush_log(dt('Done! Point your browser to !url to view your project', array('!url' => $url)), 'success');
  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }

  return TRUE;
}

function rum_create_vanilla($project_name, $project_dir) {
  try {
    // Init
    $rum = new Rum($project_name, $project_dir);

    // Create the folders in the workspace
    _rum_create_workspace_project($rum);

    // Download Drupal core
    $rum = new RumVanilla($rum);
    $options = array('Drupal 6.x', 'Drupal 7.x');
    $versions = array(RUM_CORE_VERSION_6, RUM_CORE_VERSION_7);
    $choice = drush_choice($options, dt('What Core version do you want to download?'));
    $rum->setCoreVersion($versions[$choice]);
    $rum->downloadCore();

    // Create a new host
    _rum_create_host($rum);

    // Create a new drushrc file
    $rum = new RumDrush($rum);
    $rum->createDrush();

    // Bootstrap Drupal to the ROOT phase
    $rum->bootstrap(DRUSH_BOOTSTRAP_DRUPAL_ROOT);

    // Create the Database
    $args = _rum_create_database($rum);

    // Create a settings file
    $rum = new RumSettingsFile($rum);
    $rum->createSettingsFile($args['database'], $args['user'], $args['password']);

    $url = $rum->getProjectUrl() . '/install.php';
    drush_log(dt('Done! Point your browser to !url to start installing your project', array('!url' => $url)), 'success');
  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }

  return TRUE;
}

function rum_create_repository($project_name, $project_dir) {
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

    // Set the document root of your project
    $contents = drush_scan_directory($rum->getProjectDir(), '/.*/', array('.', '..'), 0, FALSE, 'basename', 0, FALSE);
    $options = array_keys($contents);
    $choice = drush_choice($options, dt('Set the DocumentRoot of your project.'));
    $rum->setDocumentRoot($options[$choice]);

    // Configuration of your hosts file and webserver is optional. The user might
    // be running a BIND instance which handles this part of the setup.
    //
    // @see http://postpostmodern.com/instructional/a-smarter-mamp/
    $host_config = drush_confirm(dt('Do you want to add and configure a new host?'));
    if ($host_config) {
      _rum_create_host($rum);
    }

    // Create a new drushrc file
    $rum = new RumDrush($rum);
    $rum->createDrush();

    // Bootstrap Drupal to the ROOT phase
    $rum->bootstrap(DRUSH_BOOTSTRAP_DRUPAL_ROOT);

    // Create the Database
    $args = _rum_create_database($rum);

    // Create a settings file
    $rum = new RumSettingsFile($rum);
    $rum->createSettingsFile($args['database'], $args['user'], $args['password']);

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

    $url = $rum->getProjectUrl();
    drush_log(dt('Done! Point your browser to !url to view your project', array('!url' => $url)), 'success');
  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }

  return TRUE;
}

/**
 * @todo
 *
 * @param unknown_type $rum
 */
function _rum_create_workspace_project(&$rum) {
  $rum = new RumFileSystem($rum);
  $rum->createWorkSpace();
  $rum->createProjectDir();
}

/**
 * @todo
 *
 * @param unknown_type $rum
 */
function _rum_create_host(&$rum) {
  // Add a new entry in the hosts file (if we're run in DEV)
  $rum = new RumHosts($rum);
  $rum->addHostsEntry();

  // Create a new virtual host
  $rum = new RumWebServer($rum);
  $rum->createVhost();

  // Restart webserver
  $rum->restart();
}

/**
 * @todo
 *
 * @param unknown_type $rum
 * @return unknown|boolean
 */
function _rum_create_database(&$rum) {
  $rum = new RumDatabase($rum);
  $rum->setProjectDbUser();
  $rum->createUser();
  $rum->setProjectDb();
  $rum->createDatabase();

  return array(
    'database' => $rum->getProjectDb(),
    'password' => $rum->getProjectDbCred(),
    'user' => $rum->getProjectDbUser(),
  );
}
