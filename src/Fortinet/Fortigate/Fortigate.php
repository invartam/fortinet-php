<?php

namespace Fortinet\Fortigate;

use Exception;
use Fortinet\Fortigate\FortiGlobal;
use Fortinet\Fortigate\HA;
use Fortinet\Fortigate\NetDevice;
use Fortinet\Fortigate\VPN;
use Fortinet\Fortigate\Zone;
use Fortinet\Fortigate\Address;
use Fortinet\Fortigate\AddressGroup;
use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\ServiceGroup;
use Fortinet\Fortigate\VIP;
use Fortinet\Fortigate\IPPool;
use Fortinet\Fortigate\Policy\Policy;

class Fortigate {

  private $global;
  private $ha;
  private $interfaces = [];
  private $VPNs = [];
  private $zones = [];
  private $addresses = [];
  private $addressGroups = [];
  private $services = [];
  private $serviceGroups = [];
  private $policies = [];
  private $VIPs = [];
  private $IPPools = [];
  private $routes = [];
  private $ip = "";
  private $bgp;

  public function __construct($ip = "")
  {
    $this->ip = $ip;
  }

  public function extractConfig()
  {
    if (empty($this->ip)) {
      throw new Exception("No IP address set for this Fortigate", 1);
    }
  }

  public function setGlobal(FortiGlobal $global)
  {
    if (!isset($global)) {
      throw new Exception("Global object is null", 1);
    }
    $this->global = $global;
  }

  public function addNetDevice(NetDevice $if)
  {
    if (!array_key_exists($if->getName(), $this->interfaces)) {
      $this->interfaces[$if->getName()] = $if;
      return true;
    }
    throw new Exception("Interface $if->name exists", 1);
  }

  public function addVPN(VPN $vpn)
  {
    if (!array_key_exists($vpn->getName(), $this->VPNs)) {
      $this->VPNs[$vpn->getName()] = $vpn;
      return true;
    }
    throw new Exception("VPN $vpn->name exists", 1);
  }

  public function addZone(Zone $zone)
  {
    if (!array_key_exists($zone->getName(), $this->zones)) {
      if (!empty($zone->interfaces)) {
        foreach ($zone->interfaces as $if) {
          if (!array_key_exists($if->getName(), $this->interfaces)) {
            throw new Exception("Interface $if->name does not exist for Zone $zone->name", 1);
          }
        }
      }
      $this->zones[$zone->getName()] = $zone;
      return true;
    }
    throw new Exception("Zone $zone->name exists", 1);
  }

  public function addAddress(Address $addr)
  {
    if (!array_key_exists($addr->getName(), $this->addresses)) {
      $this->addresses[$addr->getName()] = $addr;
      return true;
    }
    throw new Exception("Address $addr->name exists", 1);
  }

  public function addAddressGroup(AddressGroup $addrgrp)
  {
    if (!array_key_exists($addrgrp->getName() ,$this->addressGroups)) {
      $this->addressGroups[$addrgrp->getName()] = $addrgrp;
      return true;
    }
    throw new Exception("Address Group $addrgrp->name exists", 1);
  }

  public function addService(Service $svc)
  {
    if (!array_key_exists($svc->getName(), $this->services)) {
      $this->services[$svc->getName()] = $svc;
      return true;
    }
    throw new Exception("Service $svc->name exists", 1);
  }

  public function addServiceGroup(ServiceGroup $svcgrp)
  {
    if (!array_key_exists($svcgrp->getName(), $this->serviceGroups)) {
      $this->serviceGroups[$svcgrp->getName()] = $svcgrp;
      return true;
    }
    throw new Exception("Address Group $svcgrp->name exists", 1);
  }

  public function addPolicy(Policy $policy)
  {
    foreach ($policy->srcintfs as $if) {
      if (!array_key_exists($if->getName(), $this->interfaces)
          && !array_key_exists($if->getName(), $this->zones)
          && !array_key_exists($if->getName(), $this->VPNs)
          && $if->name != "any")
      {
        throw new Exception("Source interface $if->name does not exist", 1);
      }
    }

    foreach ($policy->dstintfs as $if) {
      if (!array_key_exists($if->getName(), $this->interfaces)
          && !array_key_exists($if->getName(), $this->zones)
          && !array_key_exists($if->getName(), $this->VPNs)
          && $if->name != "any")
      {
        throw new Exception("Destination interface $if->name does not exist", 1);
      }
    }

    foreach ($policy->srcaddrs as $addr) {
      if (!array_key_exists($addr->getName(), $this->addresses)
          && !array_key_exists($addr->getName(), $this->addressGroups)
          && $addr->name != "all")
      {
        throw new Exception("Source address $addr->name does not exist", 1);
      }
    }

    foreach ($policy->dstaddrs as $addr) {
      if (!array_key_exists($addr->getName(), $this->addresses)
          && !array_key_exists($addr->getName(), $this->addressGroups)
          && !array_key_exists($addr->getName(), $this->VIPs)
          && $addr->name != "all")
      {
        throw new Exception("Destination address $addr->name does not exist", 1);
      }
    }

    foreach ($policy->services as $service) {
      if (!array_key_exists($service->getName(), $this->services)
          && !array_key_exists($service->getName(), $this->serviceGroups)
          && $service->name != "ALL")
      {
        throw new Exception("Service $service->name does not exist", 1);
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
    throw new Exception("VIP $vip->name exists", 1);
  }

  public function addIPPool(IPPool $pool)
  {
    if (!array_key_exists($pool->getName(), $this->IPPools)) {
      $this->IPPools[$pool->getName()] = $pool;
      return true;
    }
    throw new Exception("IP Pool $pool->name exists", 1);
  }

  public function addRoute(Route $route)
  {
    if (!array_key_exists($route->getDevice(), $this->interfaces) && !array_key_exists($route->getDevice(), $this->VPNs)) {
      throw new Exception("Interface " . $route->getDevice() . " does not exist", 1);
    }
    $this->routes[] = $route;
    return true;
  }

  public function setBGP(BGP $bgp)
  {
    $this->bgp = $bgp;
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __toString()
  {
    $conf = "";
    if (isset($this->global)) {
      $conf .= "config system global\n";
      $conf .= $this->global->getConf();
      $conf .= "end\n";
    }
    if (!empty($this->interfaces)) {
      $conf .= "config system interface\n";
      foreach ($this->interfaces as $if) {
        $conf .= $if->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->zones)) {
      $conf .= "config system zone\n";
      foreach ($this->zones as $zone) {
        $conf .= $zone->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->addresses)) {
      $conf .= "config firewall address\n";
      foreach ($this->addresses as $address) {
        $conf .= $address->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->addressGroups)) {
      $conf .= "config firewall addrgrp\n";
      foreach ($this->addressGroups as $addrgrp) {
        $conf .= $addrgrp->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->services)) {
      $conf .= "config firewall service custom\n";
      foreach ($this->services as $service) {
        $conf .= $service->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->serviceGroups)) {
      $conf .= "config firewall service group\n";
      foreach ($this->serviceGroups as $servicegroup) {
        $conf .= $servicegroup->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->routes)) {
      $conf .= "config router static\n";
      foreach ($this->routes as $route) {
        $conf .= $route->getConf();
      }
      $conf .= "end\n";
    }
    if (isset($this->bgp)) {
      $conf .= "config router bgp\n";
      $conf .= $this->bgp->getConf();
      $conf .= "end\n";
    }
    if (!empty($this->VIPs)) {
      $conf .= "config firewall vip\n";
      foreach ($this->VIPs as $vip) {
        $conf .= $vip->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->IPPools)) {
      $conf .= "config firewall ippool\n";
      foreach ($this->IPPools as $pool) {
        $conf .= $pool->getConf();
      }
      $conf .= "end\n";
    }
    if (!empty($this->policies)) {
      $conf .= "config firewall policy\n";
      foreach ($this->policies as $policy) {
        $conf .= $policy->getConf();
      }
      $conf .= "end\n";
    }
    return $conf;
  }
}
