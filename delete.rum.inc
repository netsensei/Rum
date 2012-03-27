<?php
/**
 * @file
 * Contains a logic to delete a project created with Rum or adhering to the Rum
 * conventions of how a project is set up in your local environment.
 */

use Rum\Component\Rum\Rum;
use Rum\Component\Rum\RumDatabase;

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

    // @todo delete the vhost configuration
    // @todo delete the link

    // @todo delete the www folder

    // @todo delete the db folder

    // @todo delete the project folder
    
    // @todo delete the drushrc file

  } catch (Exception $e) {
    drush_set_error($e->getMessage());
  }
  
}
