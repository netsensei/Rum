<?php

namespace Rum\Component\Rum;

interface RumInterface {

  public function getWorkspace();
  
  public function getProjectDomain();
  
  public function getProjectDir();
  
  public function getProjectName();

  public function getHostName();

  public function getOs();

  public function getEnvironment();

  public function getTime();

}