<?php

require_once __DIR__ . '/lib/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

/**
 * Implements hook_drush_command().
 */
function rum_drush_command() {

  rum_class_loader();

  $items = array();

  $items['rum-create'] = array(
    'description' => 'Creates a new project in your environment with a new Drupal set up',
    'arguments' => array(
      'type' => 'The type of installation. Available types: vanilla',
      'project_name' => 'The name of your project',
    ),
    'options' => array(
       'project-dir' => 'The name of the directory inside the workspace where your project will be scaffolded.',
    ),
    'aliases' => array('rc'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'examples' => array(
      'drush rum-create vanilla foobar' => 'Create a new project called foobar and download a clean Drupal core',
      'drush rum-create vanilla foobar --project-dir=foobar' => 'Create a new foobar project in the \'foobar\' directory called \'foobar\' and download a clean Drupal core',
    ),
  );

  $items['rum-delete'] = array(
    'description' => 'Delete a local project',
    'arguments' => array(
      'project' => 'The project name',
    ),
    'options' => array(
      'project-dir' => 'The name of the directory inside the workspace where your project is scaffolded.',
    ),
    'aliases' => array('rd'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'examples' => array(
      'drush rum-delete foobar' => 'Delete a project named foobar',
    ),
  );

  return $items;
}

/**
 * Initializes auto loading
 *
 * Rum is PSR-0 compliant. We use the Symfony2 autoloader to laod all the
 * Rum classes. This function is the first thing we call when Rum is bootstrapped.
 */
function rum_class_loader() {
  $loader = new UniversalClassLoader();
  $loader->registerNamespace('Rum', __DIR__ . '/lib');
  $loader->register();
}