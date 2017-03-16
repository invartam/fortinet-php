<?php
namespace Fortinet\Fortigate\VPN\IPSec;

use Fortinet\Fortigate\Policy\PolicyInterface;
use Fortinet\Fortigate\VPN\IPSec\Phase1;
use Fortinet\Fortigate\VPN\IPSec\Phase2;

class IPSec extends PolicyInterface {

  public static $authAlgorithms = [
    "sha1",
    "sha256",
    "sha384",
    "sha512"
  ];
  public static $encAlgorithms = [
    "aes128",
    "aes192",
    "aes256"
  ];
  public static $authEncAlgorithms = [
    "aes128gcm",
    "aes256gcm"
  ];
  public static $nattModes = [
    "enable",
    "disable",
    "forced"
  ];
  public static $dhGroups = [1, 2, 5, 14, 15, 16, 17, 18, 19, 20, 21];

  private $p1 = null;
  private $p2 = null;

  public function __construct(Phase1 $p1, Phase2 $p2)
  {
    $this->name = $p1->getName();
    $this->p1 = $p1;
    $this->p2 = $p2;
  }

  public function getP1()
  {
    return $this->p1;
  }
  
  public function getP2()
  {
    return $this->p2;
  }
}
