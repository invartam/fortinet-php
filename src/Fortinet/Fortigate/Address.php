<?php

namespace Fortinet\Fortigate;

use Policy\PolicyAddress as PolicyAddress;

class Address extends PolicyAddress {

  private static $ALL = null;

  private $ip = "";
  private $mask = 32;

  public static function ALL()
  {
    if (!Self::$ALL) {
      Self::$ALL = new Self("all");
    }
    return Self::$ALL;
  }

  public function __construct($name, $ip="0.0.0.0", $mask = 32)
  {
    $this->name = $name;
    $this->ip = $ip;
    $this->mask = $mask;
  }

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
}
