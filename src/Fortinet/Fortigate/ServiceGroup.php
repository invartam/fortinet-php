<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\Policy\PolicyService;

class ServiceGroup extends PolicyService {

  $name = "";
  $services = [];

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function addService(Service $service)
  {
    $this->services[] = $address;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
