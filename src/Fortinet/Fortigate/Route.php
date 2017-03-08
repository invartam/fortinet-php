<?php
namespace Fortinet\Fortigate;

class Route {

  private static $ID = 1;

  private $id = 0;
  private $net = "";
  private $gw = "";
  private $if = "";

  public function __construct($ip, $mask, $if, $gw = "")
  {
    if (empty($ip) || empty($mask) || empty($if)) {
      throw new Exception("At least IP, netmask and interface must be provided", 1);
    }
    $this->id = self::$ID++;
    $this->ip = $ip;
    $this->mask = $mask;
    $this->gw = $gw;
    $this->if = $if;
  }

  public function getConf()
  {
    $conf = "edit $this->id\n";
    $conf .= "set dst $this->ip $this->mask\n";
    $conf .= "set device \"$this->if\"\n";
    if (!empty($this->gw)) {
      $conf .= "set gateway $this->gw\n";
    }
    $conf .= "next\n";

    return $conf;
  }

  public function getDevice()
  {
    return $this->if;
  }

  public function getIP()
  {
    return $this->ip;
  }

  public function getMask()
  {
    return $this->mask;
  }

  public function getGateway()
  {
    return $this->gw;
  }
}
