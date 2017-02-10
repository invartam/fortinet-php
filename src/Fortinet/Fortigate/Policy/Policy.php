<?php

namespace Fortinet\Fortigate\Policy;

use PolicyAddress;
use PolicyService;
use PolicyInterface;

class Policy {

  private $srcintfs = [];
  private $dstintfs = [];
  private $srcaddrs = [];
  private $dstaddrs = [];
  private $services = [];

  public function addSrcInterface(PolicyInterface $if)
  {
    $this->srcintfs[] = $if->getName();
  }

  public function addDstInterface(PolicyInterface $if)
  {
    $this->dstintfs[] = $if->getName();
  }

  public function addSrcAddress(PolicyAddress $addr)
  {
    $this->srcaddrs[] = $addr->getName();
  }

  public function addDstAddress(PolicyAddress $addr)
  {
    $this->dstaddrs[] = $addr->getName();
  }

  public function addService(PolicyService $svc)
  {
    $this->services[] = $svc->getName();
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
