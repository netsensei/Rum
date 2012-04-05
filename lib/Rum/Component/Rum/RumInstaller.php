<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Rum\Exception\RumBootstrapDrupalConfigurationFailed;

class RumInstaller extends RumDecorator {

  private $rum;

  public function __construct($rum) {
    parent::__construct($rum);
  }

  public function install($profile) {
    // The project name is the alias name of our site
    $alias = '@' . $this->getProjectName();
    $site_record = drush_sitealias_get_record($alias);
    if (!drush_bootstrap_max_to_sitealias($site_record, DRUSH_BOOTSTRAP_DRUPAL_CONFIGURATION)) {
      throw new RumBootstrapDrupalConfigurationFailed();
    }
    // Make this configurable? Provide defaults!
    $options['uid1_account'] = drush_prompt(dt('Enter the administrator (uid=1) account name'));
    $options['uid1_pass'] = drush_prompt(dt('Enter the administrator (uid=1) password'));
    $options['uid1_mail'] = drush_prompt(dt('Enter the administrator (uid=1) e-mail address'));
    $options['locale'] = drush_prompt(dt('Enter your desired locale'));
    $options['site_name'] = drush_prompt(dt('Enter the name of your site'));
    $options['site_mail'] = drush_prompt(dt('Enter the global mail address of your site'));
    drush_include_engine('drupal', 'site_install', drush_drupal_major_version());
    drush_core_site_install_version($profile, $options);
  }

}