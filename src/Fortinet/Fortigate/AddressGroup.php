<?php

namespace Fortinet\Fortigate;

use Policy\PolicyAddress;
use Address;

class AddressGroup extends PolicyAddress {

  private $addresses = [];

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function addAddress(Address $address)
  {
    $this->addresses[] = $address;
  }
}
