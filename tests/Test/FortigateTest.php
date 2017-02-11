<?php

namespace Tests\Test;

require __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Fortinet\Fortigate\Fortigate;
use Fortinet\Fortigate\Fortigate\Policy\Policy;
use Fortinet\Fortigate\Address;
use Fortinet\Fortigate\AddressGroup;
use Fortinet\Fortigate\NetDevice;
use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\VIP;
use Fortinet\Fortigate\Zone;
use Fortinet\Fortigate\FortiGlobal;

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
    var_dump($fgt);
  }

  public function testAddSimpleNetDevice()
  {
    $fgt = new Fortigate();
    $port1 = new NetDevice("port1", NetDevice::PHY);
    $fgt->addNetDevice($port1);

    $this->assertEquals($fgt->interfaces["port1"]->getName(), "port1");
    $this->assertEquals($fgt->interfaces["port1"]->type, NetDevice::PHY);
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
    $vlan42->vlanID = 42;
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
}
