<?php
namespace Fortinet\Fortigate;

class FortiGlobal {

  private $hostname = "";

  public function __set($property, $value)
  {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function getConf()
  {
    print "set hostname $this->hostname\n";

    return $conf;
  }
}
