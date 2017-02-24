<?php

namespace Fortinet\Fortigate;

use Exception;
use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\Policy\PolicyService;

class ServiceGroup extends PolicyService {

  private $name = "";
  private $services = [];

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function addService(PolicyService $service)
  {
    $this->services[] = $address;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function getConf()
  {
    if (empty($this->services)){
      throw new Exception("There is no services in service group $this->name", 1);

    }
    $conf = "edit \"$this->name\"\n";
    $conf .= "set member " . implode($this->services) . "\n";
    $conf .= "next\n";

    return $conf;
  }
}
