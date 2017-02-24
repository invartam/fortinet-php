<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyInterface;
use Fortinet\Fortigate\NetDevice;

class Zone extends PolicyInterface {

  private $interfaces = [];

  public function addInterface(NetDevice $if)
  {
    $this->interfaces[] = $if;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->property;
    }
  }

  public function getConf()
  {
    if (empty($this->interfaces)){
      throw new Exception("There is no interfaces in zone $this->name", 1);
    }
    $conf = "edit $this->name\n";
    $conf .= "set intrazone allow\n";
    $conf .= "set interface " . implode($this->interfaces) . "\n";
    $conf .= "next\n";

    return $conf;
  }
}
