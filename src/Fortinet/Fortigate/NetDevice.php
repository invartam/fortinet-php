<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyInterface;

class NetDevice extends PolicyInterface {

  const PHY = 0;
  const VLAN = 1;
  const LAGG = 2;

  private static $ANY = NULL;

  private $ip = "0.0.0.0";
  private $masklength = 0;
  private $type = self::PHY;
  private $laggGroup = [];
  private $vlanID = 0;
  private $vlanDevice;

  public static function ANY()
  {
    if (!self::$ANY) {
      self::$ALL = new self("any");
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

  public function __set($property, $value)
  {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
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

  public function setVlanDevice(NetDevice $if)
  {
    $this->vlanDevice = $if;
  }

  public function getVlanDevice(NetDevice $if)
  {
    return $this->vlanDevice;
  }
}
