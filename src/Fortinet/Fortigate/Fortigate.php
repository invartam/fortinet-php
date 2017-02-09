<?php

namespace Fortinet\Fortigate;

use FortiGlobal;
use HA;
use Interface;
use Zone;
use Address;
use AddressGroup;
use Service;
use ServiceGroup;
use Policy\Policy as Policy;

class Fortigate {

  private FortiGlobal $global;
  private HA $ha;
  private $interfaces = [];
  private $zones[];
  private $addresses = [];
  private $addressGroups = [];
  private $services = [];
  private $serviceGroups = [];
  private $policies = [];
  private $VIPs = [];

  public function addInterface(Interface $if)
  {
    if (!array_key_exists($if->name, $this->interfaces)) {
      $this->interfaces[$if->name] = $if;
      return true;
    }
    return false;
  }

  public function addZone(Zone $zone)
  {
    if (!array_key_exists($zone->name, $this->zones)) {
      $this->zones[$zone->name] = $zone;
      return true;
    }
    return false;
  }

  public function addAddress(Address $addr)
  {
    if (!array_key_exists($addr->name, $this->addresses)) {
      $this->addresses[$addr->name] = $addr;
      return true;
    }
    return false;
  }

  public function addAddressGroup(AddressGroup $addrgrp)
  {
    if (!array_key_exists($addrgrp->name ,$this->addressGroups)) {
      $this->addressGroups[$addrgrp->name] = $addrgrp;
      return true;
    }
    return false;
  }

  public function addService(Service $svc)
  {
    if (!array_key_exists($svc->name, $this->services)) {
      $this->services[$svc->name] = $svc;
      return true;
    }
    return false;
  }

  public function addPolicy(Policy $policy)
  {
    foreach ($policy->srcintfs as $if) {
      if (!array_key_exists($if->name, $this->interfaces)
          && !array_key_exists($if->name, $this->zones))
      {
        print "[ERROR] Fortigate::addPolicy(): Source interface $if->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->dstintfs as $if) {
      if (!array_key_exists($if->name, $this->interfaces)
          && !array_key_exists($if->name, $this->zones))
      {
        print "[ERROR] Fortigate::addPolicy(): Destination interface $if->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->srcaddrs as $addr) {
      if (!array_key_exists($addr->name, $this->addresses)
          && !array_key_exists($addr->name, $this->addressGroups))
      {
        print "[ERROR] Fortigate::addPolicy(): Source address $addr->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->dstaddrs as $addr) {
      if (!array_key_exists($addr->name, $this->addresses)
          && !array_key_exists($addr->name, $this->addressGroups)
          && !array_key_exists($addr->name, $this->VIPs))
      {
        print "[ERROR] Fortigate::addPolicy(): Destination address $addr->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->services as $service) {
      if (!array_key_exists($service->name, $this->services)
          && !array_key_exists($service->name, $this->serviceGroups))
      {
        print "[ERROR] Fortigate::addPolicy(): Service $service->name does not exist\n";
        return false;
      }
    }

    $this->policies[] = $policy;
    return true;
  }

  public function addVIP(VIP $vip)
  {
    if (!array_key_exists($vip->name, $this->VIPs)) {
      $this->VIPs[$vip->name] = $vip;
      return true;
    }
    return false;
  }

  public function __get($property, $value)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
