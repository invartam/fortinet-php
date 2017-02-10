<?php

namespace Fortinet\Fortigate

class FortiGlobal {

  $hostname = "";

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
