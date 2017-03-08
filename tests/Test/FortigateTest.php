<?php

namespace Tests\Test;

require __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Fortinet\Fortigate\Fortigate;
use Fortinet\Fortigate\Policy\Policy;
use Fortinet\Fortigate\Address;
use Fortinet\Fortigate\AddressGroup;
use Fortinet\Fortigate\NetDevice;
use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\VIP;
use Fortinet\Fortigate\IPPool;
use Fortinet\Fortigate\Zone;
use Fortinet\Fortigate\FortiGlobal;
use Fortinet\Fortigate\Route;

class FortigateTest extends TestCase {

  public function testAddAddress()
  {
    $fgt = new Fortigate();
    $addr = new Address("test", "192.168.1.254", 24);
    $this->assertEquals($addr->getName(), "test");
    $this->assertEquals($addr->ip, "192.168.1.254");
    $this->assertTrue($fgt->addAddress($addr));
  }

  public function testAddAddressGroup()
  {
    $fgt = new Fortigate();
    $grp = new AddressGroup("GN-TEST");
    $grp->addAddress(new Address("test", "192.168.1.254", 24));
    $grp2 = new AddressGroup("GN-TEST2");
    $grp2->addAddress($grp);
    $this->assertEquals($grp->getName(), "GN-TEST");
    $this->assertEquals($grp->addresses[0], "test");
    $this->assertEquals($grp2->addresses[0], "GN-TEST");
    $this->assertTrue($fgt->addAddressGroup($grp));
    $this->assertTrue($fgt->addAddressGroup($grp2));
  }

  public function testAddSimpleNetDevice()
  {
    $fgt = new Fortigate();
    $port1 = new NetDevice("port1", NetDevice::PHY);
    $port1->addAccess(NetDevice::ACCESS_PING);
    $fgt->addNetDevice($port1);

    $this->assertEquals($fgt->interfaces["port1"]->getName(), "port1");
    $this->assertEquals($fgt->interfaces["port1"]->type, NetDevice::PHY);
    $this->assertEquals(implode($fgt->interfaces["port1"]->access), "ping");
  }

  public function testVlanLaggInterface()
  {
    $fgt = new Fortigate();
    $port1 = new NetDevice("port1", NetDevice::PHY);
    $port2 = new NetDevice("port2", NetDevice::PHY);
    $lagg1 = new NetDevice("lagg1", NetDevice::LAGG);
    $lagg1->addLaggNetDevice($port1);
    $lagg1->addLaggNetDevice($port2);
    $vlan42 = new NetDevice("vlan42", NetDevice::VLAN, "192.168.42.254", 24);
    $vlan42->setVlanDevice($lagg1);
    $vlan42->setVlanID(42);
    $fgt->addNetDevice($port1);
    $fgt->addNetDevice($port2);
    $fgt->addNetDevice($lagg1);
    $fgt->addNetDevice($vlan42);

    $this->assertEquals($fgt->interfaces["vlan42"]->getName(), "vlan42");
    $this->assertEquals($fgt->interfaces["vlan42"]->type, NetDevice::VLAN);
    $this->assertEquals($fgt->interfaces["vlan42"]->vlanID, 42);
    $this->assertEquals($fgt->interfaces["vlan42"]->vlanDevice->type, NetDevice::LAGG);
    $this->assertEquals($fgt->interfaces["vlan42"]->vlanDevice->laggGroup[0]->type, NetDevice::PHY);
    $this->assertEquals($fgt->interfaces["vlan42"]->vlanDevice->laggGroup[1]->type, NetDevice::PHY);
    $this->assertEquals($fgt->interfaces["lagg1"], $lagg1);
    $this->assertEquals($fgt->interfaces["port1"], $port1);
    $this->assertEquals($fgt->interfaces["port2"], $port2);
  }

  public function testPolicy()
  {
    $fgt = new Fortigate();
    $fgt->addNetDevice(new NetDevice("port1", NetDevice::PHY, "192.168.1.1", 24));
    $fgt->addNetDevice(new NetDevice("port2", NetDevice::PHY, "192.168.2.1", 24));
    $fgt->addAddress(new Address("LAN1", "192.168.1.0", "255.255.255.0"));
    $fgt->addAddress(new Address("LAN2", "192.168.2.0", "255.255.255.0"));
    $policy = new Policy();
    $policy->addSrcInterface($fgt->interfaces["port1"]);
    $policy->addDstInterface($fgt->interfaces["port2"]);
    $policy->addSrcAddress($fgt->addresses["LAN1"]);
    $policy->addDstAddress($fgt->addresses["LAN2"]);
    $policy->addService(Service::ALL());
    $policy->doNAT();
    $fgt->addPolicy($policy);

    $conf = "edit 1000\nset srcintf port1\nset dstintf port2\nset srcaddr LAN1\nset dstaddr LAN2\nset service ALL\nset logtraffic all\nset action accept\nset schedule always\nset nat enable\nnext\n";
    $this->assertEquals($policy->getConf(), $conf);
    $policy2 = new Policy();
    $this->assertEquals($policy2->id, 1001);

    //print $fgt;
  }

  public function testGlobal()
  {
    $fgt = new Fortigate();
    $global = new FortiGlobal();
    $global->hostname = "TEST";
    $fgt->setGlobal($global);
    $this->assertEquals($fgt->global->hostname, "TEST");
  }

  public function testIPPool()
  {
    $fgt = new Fortigate();
    $fgt->addNetDevice(new NetDevice("port1", NetDevice::PHY, "192.168.1.1", 24));
    $fgt->addNetDevice(new NetDevice("port2", NetDevice::PHY, "192.168.2.1", 24));
    $fgt->addAddress(new Address("LAN1", "192.168.1.0", "255.255.255.0"));
    $fgt->addAddress(new Address("LAN2", "192.168.2.0", "255.255.255.0"));
    $fgt->addIPPool(new IPPool("POOL1", IPPool::TYPE_ONETOONE, "3.3.3.3"));
    $policy = new Policy();
    $policy->addSrcInterface($fgt->interfaces["port1"]);
    $policy->addDstInterface($fgt->interfaces["port2"]);
    $policy->addSrcAddress($fgt->addresses["LAN1"]);
    $policy->addDstAddress($fgt->addresses["LAN2"]);
    $policy->addService(Service::ALL());
    $policy->doNAT($fgt->IPPools["POOL1"]);
    $fgt->addPolicy($policy);

    $this->assertEquals($policy->NATPool->getName(), "POOL1");
  }

  public function testRoute()
  {
    $fgt = new Fortigate();
    $fgt->addNetDevice(new NetDevice("port1", NetDevice::PHY, "192.168.1.1", 24));
    $fgt->addRoute(new Route("192.168.0.0", "255.255.255.0", "port1", "192.168.1.254"));

    $this->assertEquals($fgt->routes[0]->getDevice(), "port1");
  }
}
