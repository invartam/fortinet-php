<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyInterface;

class NetDevice extends PolicyInterface {

  const PHY = 0;
  const VLAN = 1;
  const LAGG = 2;

  const ACCESS_PING = "ping";
  const ACCESS_SSH = "ssh";
  const ACCESS_HTTP = "http";
  const ACCESS_HTTPS = "https";

  private static $ANY = NULL;

  private $masklength = 0;
  private $type = self::PHY;
  private $laggGroup = [];
  private $access = [];
  private $vlanID = 0;
  private $vdom = "root";
  private $vlanDevice;
  private $alias = "";

  public static function ANY()
  {
    if (!self::$ANY) {
      self::$ANY = new self("any");
    }
    return self::$ANY;
  }

  public function __construct($name, $type = self::PHY, $ip = "0.0.0.0", $masklength = 0)
  {
    $this->name = $name;
    $this->type = $type;
    $this->ip = $ip;
    $this->masklength = $masklength;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function addLaggNetDevice(NetDevice $if)
  {
    $this->laggGroup[] = $if;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function setIP($ip)
  {
    if (count(explode(".", $ip)) != 4) {
      throw new Exception("Error, IP is incorrect for interface $this->name", 1);
    }
    $this->ip = $ip;
  }

  public function setMask($mask)
  {
    $this->masklength = $mask;
  }

  public function setVlanID($id)
  {
    $this->vlanID = $id;
  }

  public function setVlanDevice(NetDevice $if)
  {
    $this->vlanDevice = $if;
  }

  public function setVdom($vdom)
  {
    $this->vdom = $vdom;
  }

  public function setAlias($alias)
  {
    $this->alias = $alias;
  }

  public function getVlanDevice(NetDevice $if)
  {
    return $this->vlanDevice;
  }

  public function addAccess($access)
  {
    if (!in_array($access, [self::ACCESS_PING, self::ACCESS_SSH, self::ACCESS_HTTP, self::ACCESS_HTTPS])) {
      throw new Exception("Access $access is not supported", 1);
    }
    $this->access[] = $access;
  }

  public function getConf()
  {
    $conf = "edit $this->name\n";
    $conf .= "set vdom $this->vdom\n";
    $conf .= "set ip $this->ip/$this->masklength\n";
    if ($this->type == self::VLAN){
      $conf .= "set type vlan\n";
      $conf .= "set vlanid $this->vlanID\n";
      $conf .= "set interface $this->vlanDevice\n";
    }
    if ($this->type == self::LAGG){
      $conf .= "set type agg\n";
      $conf .= "set intf " . implode($this->laggGroup) . "\n";
    }
    if (!empty($this->alias)) {
      $conf .= "set alias $this->alias\n";
    }
    if (!empty($this->access)) {
      $conf .= "set allowaccess " . implode(" ", $this->access) . "\n";
    }
    $conf .= "next\n";

    return $conf;
  }
}
