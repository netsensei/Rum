<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\State\State;
use Rum\Component\State\Git;
use Rum\Component\FileSystem\FileSystem;

class RumState extends RumDecorator {
  
  private $state;
  
  const RUM_STATE_SVN = 'Subversion';
  
  const RUM_STATE_GIT = 'Git';

  public function __construct($rum, $type) {
    parent::__construct($rum);
    switch ($type) {
      case self::RUM_STATE_SVN :
      case self::RUM_STATE_GIT :
        $this->state = State::getInstance($type);
        break;
      default:
    }
  }

  public function fetch($repository, $working_directory = NULL) {
    drush_log(dt('Fetching project data from remote source...'), 'status');
    if (!$working_directory) {
      $working_directory = $this->getProjectDir();
    }
    $this->state->fetch($repository, $working_directory);

    return TRUE;
  }
}