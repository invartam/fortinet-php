<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyInterface;

class NetDevice extends PolicyInterface {

  const PHY = 0;
  const VLAN = 1;
  const LAGG = 2;

  private static $ANY = NULL;

  private $masklength = 0;
  private $type = self::PHY;
  private $laggGroup = [];
  private $vlanID = 0;
  private $vdom = "root";
  private $vlanDevice;

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

  public function setVdom($vdom)
  {
    $this->vdom = $vdom;
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

  public function setVlanID($id)
  {
    $this->vlanID = $id;
  }

  public function setVlanDevice(NetDevice $if)
  {
    $this->vlanDevice = $if;
  }

  public function getVlanDevice(NetDevice $if)
  {
    return $this->vlanDevice;
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
    $conf .= "next\n";

    return $conf;
  }
}
