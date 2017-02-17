<?php

namespace Fortinet\Fortigate\Policy;

use Exception;
use Fortinet\Fortigate\Policy\PolicyAddress;
use Fortinet\Fortigate\Policy\PolicyService;
use Fortinet\Fortigate\Policy\PolicyInterface;

class Policy {

  private static $NEXTID = 1000;

  private $id = 0;
  private $srcintfs = [];
  private $dstintfs = [];
  private $srcaddrs = [];
  private $dstaddrs = [];
  private $services = [];
  private $NAT = false;
  private $action = "accept";

  public function __construct()
  {
    $this->id = self::$NEXTID;
    self::$NEXTID++;
  }

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

  public function doNat()
  {
    $this->NAT = true;
  }

  public function setAction($action)
  {
    if (!in_array($action, ["accept", "deny"])){
      throw new Exception("Invalid action defined in policy $this->id: $action", 1);
    }
    $this->action = $action;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function getConf()
  {
    if (empty($this->srcintfs)){
      throw new Exception("There is no source interfaces in policy $this->id", 1);
    }
    if (empty($this->dstintfs)){
      throw new Exception("There is no destination interfaces in policy $this->id", 1);
    }
    if (empty($this->srcaddrs)){
      throw new Exception("There is no source addresses in policy $this->id", 1);
    }
    if (empty($this->dstaddrs)){
      throw new Exception("There is no destination interfaces in policy $this->id", 1);
    }
    if (empty($this->services)){
      throw new Exception("There is no services in policy $this->id", 1);
    }
    $conf = "edit $this->id\n";
    $conf .= "set srcintf " . implode(" ", $this->srcintfs) . "\n";
    $conf .= "set dstintf " . implode(" ", $this->dstintfs) . "\n";
    $conf .= "set srcaddr " . implode(" ", $this->srcaddrs) . "\n";
    $conf .= "set dstaddr " . implode(" ", $this->dstaddrs) . "\n";
    $conf .= "set service " . implode(" ", $this->services) . "\n";
    $conf .= "set action $this->action\n";
    if ($this->NAT){
      $conf .= "set nat enable\n";
    }
    $conf .= "next\n";

    return $conf;
  }
}
