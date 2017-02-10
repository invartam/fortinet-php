<?php

namespace Fortinet\Fortigate;

use Fortinet\Fortigate\FortiGlobal;
use Fortinet\Fortigate\HA;
use Fortinet\Fortigate\NetDevice;
use Fortinet\Fortigate\Zone;
use Fortinet\Fortigate\Address;
use Fortinet\Fortigate\AddressGroup;
use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\ServiceGroup;
use Fortinet\Fortigate\Policy\Policy;

class Fortigate {

  private $global;
  private $ha;
  private $interfaces = [];
  private $zones = [];
  private $addresses = [];
  private $addressGroups = [];
  private $services = [];
  private $serviceGroups = [];
  private $policies = [];
  private $VIPs = [];

  public function addNetDevice(NetDevice $if)
  {
    if (!array_key_exists($if->getName(), $this->interfaces)) {
      $this->interfaces[$if->getName()] = $if;
      return true;
    }
    return false;
  }

  public function addZone(Zone $zone)
  {
    if (!array_key_exists($zone->getName(), $this->zones)) {
      $this->zones[$zone->getName()] = $zone;
      return true;
    }
    return false;
  }

  public function addAddress(Address $addr)
  {
    if (!array_key_exists($addr->getName(), $this->addresses)) {
      $this->addresses[$addr->getName()] = $addr;
      return true;
    }
    return false;
  }

  public function addAddressGroup(AddressGroup $addrgrp)
  {
    if (!array_key_exists($addrgrp->getName() ,$this->addressGroups)) {
      $this->addressGroups[$addrgrp->getName()] = $addrgrp;
      return true;
    }
    return false;
  }

  public function addService(Service $svc)
  {
    if (!array_key_exists($svc->getName(), $this->services)) {
      $this->services[$svc->getName()] = $svc;
      return true;
    }
    return false;
  }

  public function addPolicy(Policy $policy)
  {
    foreach ($policy->srcintfs as $if) {
      if (!array_key_exists($if->getName(), $this->NetDevices)
          && !array_key_exists($if->getName(), $this->zones)
          && $if->name != "all")
      {
        print "[ERROR] Fortigate::addPolicy(): Source NetDevice $if->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->dstintfs as $if) {
      if (!array_key_exists($if->getName(), $this->NetDevices)
          && !array_key_exists($if->getName(), $this->zones)
          && $if->name != "all")
      {
        print "[ERROR] Fortigate::addPolicy(): Destination NetDevice $if->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->srcaddrs as $addr) {
      if (!array_key_exists($addr->getName(), $this->addresses)
          && !array_key_exists($addr->getName(), $this->addressGroups)
          && $addr->name != "any")
      {
        print "[ERROR] Fortigate::addPolicy(): Source address $addr->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->dstaddrs as $addr) {
      if (!array_key_exists($addr->getName(), $this->addresses)
          && !array_key_exists($addr->getName(), $this->addressGroups)
          && !array_key_exists($addr->getName(), $this->VIPs)
          && $addr->name != "any")
      {
        print "[ERROR] Fortigate::addPolicy(): Destination address $addr->name does not exist\n";
        return false;
      }
    }

    foreach ($policy->services as $service) {
      if (!array_key_exists($service->getName(), $this->services)
          && !array_key_exists($service->getName(), $this->serviceGroups)
          && $service->name != "ALL")
      {
        print "[ERROR] Fortigate::addPolicy(): Service $service->getName() does not exist\n";
        return false;
      }
    }

    $this->policies[] = $policy;
    return true;
  }

  public function addVIP(VIP $vip)
  {
    if (!array_key_exists($vip->getName(), $this->VIPs)) {
      $this->VIPs[$vip->getName()] = $vip;
      return true;
    }
    return false;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }
}
