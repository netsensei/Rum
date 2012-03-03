<?php

require_once __DIR__ . '/lib/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

/**
 * Implements hook_drush_command().
 */
function rum_drush_command() {
  
  rum_class_loader();

  $items['rum-create'] = array(
    'description' => 'Creates a local Drupal site',
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'examples' => array(
      'drush rum-create-vanilla' => 'Create an empty website with a clean Drupal core setup',
    ),
    'arguments' => array(
      'type' => 'The type of installation',
      'project' => 'The site name',
    ),
    'aliases' => array('rc'),
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