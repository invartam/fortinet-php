<?php

namespace Fortinet\Fortigate;

use Policy\PolicyAddress as PolicyAddress;

class Address extends PolicyAddress {

  private $ip = "";
  private $mask = 32;

  public function __construct($name, $ip, $mask = 32)
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

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
