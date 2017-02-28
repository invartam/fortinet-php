<?php
namespace Fortinet\Fortigate;

use Fortinet\Fortigate\Policy\PolicyInterface;

class VPN extends PolicyInterface {

  public function __construct($name) {
    $this->name = $name;
  }
}
