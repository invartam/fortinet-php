<?php

namespace Fortinet\Fortigate;

class HA {

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
