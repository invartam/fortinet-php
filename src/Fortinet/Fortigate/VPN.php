<?php
namespace Fortinet\Fortigate\VPN;

use Policy\PolicyInterface;

class VPN extends PolicyInterface {

  public function __construct($name) {
    $this->name = $name;
  }
}
