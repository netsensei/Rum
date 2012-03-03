<?php

use Rum\Component\Rum\Rum;
use Rum\Component\Rum\RumFileSystem;
use Rum\Component\Rum\RumVanilla;
use Rum\Component\Rum\RumWebServer;

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
    //$msg = dt('What Core version do you want to download?');
    //$version = drush_choice(array('6', '7'), $msg);
    //$rum->downloadCore($version);

    // Create a new virtual host
    $rum = new RumWebServer($rum);
    $rum->createVhost();

    // Tear down Rum
    // $rum->tearDown();
  } catch(Exception $e) {
    drush_set_error($e->getMessage());
  }
}