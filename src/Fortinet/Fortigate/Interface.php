<?php

namespace Fortigate\Fortinet;

class Interface {

  private static $ANY = null;

  private $name = "";
  private $ip = "0.0.0.0";
  private $masklength = 0;
  private $type = "physical";
  private $laggGroup = [];
  private $vlanID = 0;

  public static function ANY()
  {
    if (!Self::$ANY) {
      Self::$ALL = new Self("any");
    }
    return Self::$ANY;
  }

  public function __construct($name, $type = "physical", $ip = "0.0.0.0", $masklength = 0)
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

  public function addLaggInterface(Interface $if)
  {
    $this->laggGroup[] = $if;
  }
}
