<?php

namespace Rum\Component\State;

use Rum\Component\State\State;
use Rum\Component\FileSystem\FileSystem;
use Rum\Component\State\Exception\RumRepositoryNotCheckedOutException;

class Subversion extends State {

  const IGNORE_FILE = '.svnignore';

  public function fetch($repository, $working_directory) {
    if (!drush_shell_exec("svn co $repository $working_directory")) {
      throw new RumRepositoryNotCheckedOutException($repository, $working_directory);
    }
  }

  public function getIgnoreFile() { }

  public function createIgnoreFile($working_directory) { }
}