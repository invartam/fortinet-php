<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyAddress;
use Fortinet\Fortigate\Policy\PolicyInterface;

class VIP extends PolicyAddress {

  private $extip = "";
  private PolicyInterface $extintf;
  private $mappedip = "";

  public function __construct($name, $extip, $mappedip, PolicyInterface $extintf)
  {
    $this->name = $name;
    $this->extip = $extip;
    $this->extintf = $extintf;
    $this->mappedip = $mappedip;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function getConf()
  {
    $conf = "edit $this->name\n";
    $conf .= "set extip $this->extip\n";
    $conf .= "set extintf $this->extintf\n";
    $conf .= "set mappedip $this->mappedip\n";
    $conf .= "next\n";

    return $conf;
  }
}
