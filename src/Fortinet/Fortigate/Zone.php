<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyInterface;
use Fortinet\Fortigate\Interface;

class Zone extends PolicyInterface {

  private $interfaces = [];

  public function addInterface(Interface $if)
  {
    $this->interfaces[] = $if;
  }
}
