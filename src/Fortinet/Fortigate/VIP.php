<?php

namespace Fortinet\Fortigate;

use Policy\PolicyAddress as PolicyAddress;
use Policy\PolicyInterface as PolicyInterface;

class VIP extends PolicyAddress {

  private $extip = "";
  private PolicyInterface $extintf;
  private $mappedip = "";

  public function __construct($extip, PolicyInterface $extintf, $mappedip)
  {
    $this->extip = $extip;
    $this->extintf = $extintf;
    $this->mappedip = $mappedip;
  }

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
