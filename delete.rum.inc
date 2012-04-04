<?php
/**
 * @file
 * Contains a logic to delete a project created with Rum or adhering to the Rum
 * conventions of how a project is set up in your local environment.
 */

use Rum\Component\Rum\Rum;
use Rum\Component\Rum\RumDatabase;
use Rum\Component\Rum\RumHosts;
use Rum\Component\Rum\RumFileSystem;
use Rum\Component\Rum\RumDrush;

function drush_rum_delete($project = NULL) {
  
  // Get the project dir from the root property in the drushrc configuration
  $project = drush_get_option('rum_project_name', '');
  $project_dir = drush_get_option('rum_project_dir', '');

  try {
    $rum = new Rum($project, $project_dir);

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
      $rum->dropDatabase(); // does not compute...?
    }

    // @todo delete the hosts reference
    $rum = new RumHosts($rum);
    $rum->removeHostsEntry();

    // Delete the vhost configuration
    // Delete the link
    $rum = new RumWebServer($rum);
    $rum->removeVhost();

    // @todo delete the db folder

    // @todo delete the drushrc file
    // Delete the entire project folder
    $rum = new RumFileSystem($rum);
    $rum->RemoveProjectDir();

    $rum = new RumDrush($rum);
    $rum->removeDrush();

  } catch (Exception $e) {
    drush_set_error($e->getMessage());
  }
  
}
