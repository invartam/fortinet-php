<?php

namespace Fortinet\Fortigate;

class Service {

  private $name = "";
  private $sport = 0;
  private $dport = 0;
  private $proto = "tcp";

  public function __construct($name, $proto = "icmp", $dport = 0, $sport = 0)
  {
    $this->name = $name;
    $this->proto = $proto;
    $this->dport = $dport;
    $this->sport = $sport;
  }

  public function __set($property, $value)
  {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
  }

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
