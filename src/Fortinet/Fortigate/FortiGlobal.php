<?php

namespace Fortinet\Fortigate

class FortiGlobal {

  $hostname = "";

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
