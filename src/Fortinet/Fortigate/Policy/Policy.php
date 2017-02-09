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
    $this->srcintfs[] = $if;
  }

  public function addDstInterface(PolicyInterface $if)
  {
    $this->dstintfs[] = $if;
  }

  public function addSrcAddress(PolicyAddress $addr)
  {
    $this->srcaddrs[] = $addr;
  }

  public function addDstAddress(PolicyAddress $addr)
  {
    $this->dstaddrs[] = $addr;
  }

  public function addService(PolicyService $svc)
  {
    $this->services[] = $svc;
  }

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
