<?php

namespace Tests\Test;

require __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Fortinet\Fortigate\Fortigate;
use Fortinet\Fortigate\Fortigate\Policy\Policy;
use Fortinet\Fortigate\Address;
use Fortinet\Fortigate\AddressGroup;
use Fortinet\Fortigate\Interface;
use Fortinet\Fortigate\Service;
use Fortinet\Fortigate\VIP;
use Fortinet\Fortigate\Zone;
use Fortinet\Fortigate\FortiGlobal;

class FortigateTest extends TestCase {

  public function testAddAddress()
  {
    $fgt = new Fortigate();
    $addr = new Address("test", "192.168.1.254", 24);
    $this->assertEqual($addr->getName(), "test");
    $this->assertEqual($addr->ip, "192.168.1.254");
    $this->assertTrue($fgt->addAddress($addr));
  }

  public function testAddAddressGroup()
  {
    $fgt = new Fortigate();
    $grp = new AddressGroup("GN-TEST");
    $grp->addAddress(new Address("test", "192.168.1.254", 24));
    $this->assertEqual($grp->getName(), "GN-TEST");
    $this->assertTrue($fgt->addAddressGroup($grp));
  }
}
