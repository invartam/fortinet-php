<?php

namespace Fortinet\Fortigate;

class HA {

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
