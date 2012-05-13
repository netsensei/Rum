<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\State\State;
use Rum\Component\State\Git;

class RumState extends RumDecorator {
  
  private $state;
  
  const RUM_STATE_SVN = 'Subversion';
  
  const RUM_STATE_GIT = 'Git';

  public function __construct(Rum $rum, $type) {
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
    if (!$working_directory) {
      $working_directory = $this->getProjectDir();
    }
    $this->state->fetch($repository, $working_directory);
  }
}