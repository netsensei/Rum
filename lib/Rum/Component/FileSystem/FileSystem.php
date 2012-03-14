<?php

namespace Rum\Component\FileSystem;

use Rum\Component\FileSystem\Exception\FileSystemDirectoryCreateException;
use Rum\Component\FileSystem\Exception\FileSystemDirectoryWritableException;

class FileSystem {

  public function checkDir($directory) {
    $return = FALSE;

    if (is_dir($directory)) {
      if (!is_writable($directory)) {
        throw new FileSystemDirectoryWritableException($directory);
      }

      $return = TRUE;
    }

    return $return;
  }
  
  public function createDir($directory) {
    $return = drush_op('mkdir', $directory);
    if ($return) {
      drush_log(dt('Created %directory', array('%directory' => $directory)), 'success');
    } else {
      throw new FileSystemDirectoryCreateException($directory);
    }

    return $return; 
  }

  public function createLink($source, $target) {
    $result = drush_shell_exec("sudo ln -s $source $target");
    if (!$result) {
      throw new FileSystemCouldNotCreateLink($source, $target);
    }
  }

  public function checkFile($file) {
    if (file_exists($file)) {
      return TRUE;
    }

    return FALSE;
  }
 
  public function createFile($file, $contents) {
    $tmp_file = drush_save_data_to_temp_file($contents);

    if ($tmp_file) {
      if (drush_op('copy', $tmp_file, $file)) {
        drush_log(dt('Created %file', array('%file' => $file)), 'success');
        return TRUE;
      } else {
        // @throw copy failed
      }
    } else {
      // @throw tmpfile failed
    }
  }

  public function removeFile($file) {
    if (file_exists($file)) {
      if (is_dir($file)) {
        drush_delete_dir($file);
      }
      else {
        drush_op('unlink', $file);
        unlink($file);
      }
    }
  }
}