<?php

namespace Rum\Component\State;

use Rum\Component\State\State;
use Rum\Component\State\Exception\RumRepositoryNotClonedException;

class Git extends State {

  public function fetch($repository, $working_directory) {
    if (!drush_shell_exec("git clone $repository $working_directory")) {
      throw new RumRepositoryNotClonedException($repository, $working_directory);
    }
  }

}