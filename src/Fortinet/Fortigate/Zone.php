<?php

namespace Fortinet\Fortigate;

use Policy\PolicyInterface as PolicyInterface;
use Interface;

class Zone extends PolicyInterface {

  private $interfaces = [];

  public function addInterface(Interface $if)
  {
    $this->interfaces[] = $if;
  }
}
