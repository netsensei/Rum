<?php
/**
 * @file
 * Contains a logic to delete a project created with Rum or adhering to the Rum
 * conventions of how a project is set up in your local environment.
 */

use Rum\Component\Rum\Rum;
use Rum\Component\Rum\RumDatabase;
use Rum\Component\Rum\RumHosts;
use Rum\Component\Rum\RumWebServer;
use Rum\Component\Rum\RumFileSystem;
use Rum\Component\Rum\RumDrush;

function drush_rum_delete($project_name = '') {

  // Precedence to the @alias settings. If the project was not generated with Rum,
  // we should still be able to delete it. So we use the options and arguments.
  $alias_project_name = drush_get_option('rum_project_name', '');
  if (!empty($alias_project_name)) {
    $project_name = $alias_project_name;
  }

  if (empty($project_name)) {
    drush_set_error(dt('No project specified. Pass a project name as an argument.'));
  }

  $alias_project_dir = drush_get_option('rum_project_dir', '');
  if (!empty($alias_project_dir)) {
    $project_dir = $alias_project_dir;
  } else {
    $project_dir = drush_get_option('project-dir', '');
    if (empty($project_dir)) {
      $project_dir = $project_name;
    }
  }

  try {
    $rum = new Rum($project_name, $project_dir);

    // Environment
    $environment = drush_get_option('rum_environment', '');
    $rum->setEnviroment($environment);

    // Delete the db user & db
    global $databases;
    $rum = new RumDatabase($rum);
    $db_user = $databases['default']['default']['username'];
    $rum->setProjectDbUser($databases['default']['default']['username'], $databases['default']['default']['password']);
    $rum->setProjectDb($databases['default']['default']['database']);
    $result = drush_confirm(dt('Do you really want to drop the database?'));
    if ($result) {
      $rum->dropUser();
      $rum->dropDatabase();
    } else {
      return drush_user_abort();
    }

    // Delete the hosts reference
    $rum = new RumHosts($rum);
    $rum->removeHostsEntry();

    // Delete the vhost configuration
    // Delete the link
    $rum = new RumWebServer($rum);
    $rum->removeVhost();

    // Restart webserver
    $rum->restart();

    // Delete the entire project folder
    $rum = new RumFileSystem($rum);
    $rum->RemoveProjectDir();

    // Delete the drushrc file
    $rum = new RumDrush($rum);
    $rum->removeDrush();

  } catch (Exception $e) {
    drush_set_error($e->getMessage());
  }
}
