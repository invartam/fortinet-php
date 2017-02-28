<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyAddress;

class Address extends PolicyAddress {

  private static $ALL = null;

  private $ip = "0.0.0.0";
  private $mask = "255.255.255.255";
  private $range = false;

  public static function ALL()
  {
    if (!self::$ALL) {
      self::$ALL = new self("all");
    }
    return self::$ALL;
  }

  public function __construct($name, $ip="0.0.0.0", $mask = "255.255.255.255", $range = false)
  {
    $this->name = $name;
    $this->ip = $ip;
    $this->mask = $mask;
    $this->range = $range;
  }

 function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  function getConf()
  {
    $conf = "edit $this->name\n";
    if ($this->range) {
      $conf .= "set type iprange\n";
      $conf .= "set start-ip $this->ip\n";
      $conf .= "set end-ip $this->mask\n";
    }
    else {
      $conf .= "set subnet $this->ip $this->mask\n";
    }
    $conf .= "next\n";
    return $conf;
  }
}
