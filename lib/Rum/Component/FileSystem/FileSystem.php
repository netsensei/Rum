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
      drush_log(dt('Created @directory', array('@directory' => $directory)), 'success');
    } else {
      throw new FileSystemDirectoryCreateException($directory);
    }

    return $return;
  }

  public function removeDir($directory) {
    $return = drush_delete_dir($directory, TRUE); // forced removal
    if ($return) {
      drush_log(dt('Deleted @directory'), array('@directory' => $directory), 'success');
    } else {
      throw new FileSystemDirectoryRemoveException($directory);
    }
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
        drush_log(dt('Created @file', array('@file' => $file)), 'success');
        return TRUE;
      } else {
        // @throw copy failed
      }
    } else {
      // @throw tmpfile failed
    }
  }

  public function removeFile($file) {
    $success = FALSE;

    if (is_link($file)) {
      $success = unlink($file);
    } else if (is_dir($file)) {
      $succes = drush_delete_dir($file);
    } else if (is_file($file)) {
      $success = unlink($file);
    }

    if ($success) {
      drush_log(dt('Removed @file', array('@file' => $file)), 'success');
    } else {
      // @throw file could not be removed
    }
  }

  public static function sanitize($filename) {
    // Code from file_create_filename().
    // Strip control characters (ASCII value < 32). Though these are allowed in
    // some filesystems, not many applications handle them well.
    $filename = preg_replace('/[\x00-\x1F]/u', '_', $filename);
    if (substr(PHP_OS, 0, 3) == 'WIN') {
      // These characters are not allowed in Windows filenames
      $filename = str_replace(array(':', '*', '?', '"', '<', '>', '|'), '_', $filename);
    }

    return $filename;
  }
}