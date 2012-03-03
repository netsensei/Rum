<?php

namespace Rum\Component\Rum\Exception;

class RumSettingDoesNotExist extends \Exception {

  function __construct($setting) {
    $message = dt('The setting "%setting" is not configured in your .drushrc.php', array('%setting' => $setting));
    parent::__construct($message);
  }

}
