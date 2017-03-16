<?php
namespace Fortinet\Fortigate\VPN\IPSec;

use Fortinet\Fortigate\Policy\PolicyObject;
use Fortinet\Fortigate\Policy\PolicyInterface;
use Fortinet\Fortigate\VPN\IPSec\IPSec;

class Phase1 extends PolicyObject {

  private $if;
  // private $localid = "0.0.0.0";
  private $remotegw = "0.0.0.0";
  private $psk = "";
  private $ikeversion = 1;
  private $auth = "aes128";
  private $encryption = "sha256";
  private $dhgrp = 14;
  private $keylifetime = 86400;
  private $natt = "disable";

  public function __construct($name, PolicyInterface $if, $remotegw, $psk, $natt = "disable", $ikeversion = 1, $auth = "sha256", $encryption = "aes128", $dhgrp = 14, $keylifetime = 86400) {
    $this->name = $name;
    if (count(explode(".", $remotegw)) != 4) {
      throw new Exception("Remote gateway must be an IP address", 1);
    }
    $this->if = $if;
    $this->remotegw = $remotegw;
    $this->psk = $psk;
    if ($ikeversion < 1 || $ikeversion > 2) {
      throw new Exception("IKE Version must be set to 1 or 2", 1);
    }
    $this->ikeversion = $ikeversion;
    if (!in_array($natt, IPSec::$nattModes)) {
      throw new Exception("NAT-T mode $natt not supported", 1);   
    }
    $this->natt = $natt;
    if (!in_array($auth, IPSec::$authAlgorithms) && !in_array($auth, IPSec::$authEncAlgorithms)) {
      throw new Exception("Authentication algorithm $auth is not supported", 1);
    }
    $this->auth = $auth;
    if (!in_array($encryption, IPSec::$encAlgorithms)) {
      throw new Exception("Encryption algorithm $encryption is not supported", 1);
    }
    if (in_array($auth, IPSec::$authEncAlgorithms)) {
      $this->encryption = $auth;
    }
    else {
      $this->encryption = $encryption;
    }
    if (!in_array($dhgrp, IPSec::$dhGroups)) {
      throw new Exception("DH Group $dhgrp not supported", 1);    
    }
    $this->dhgrp = $dhgrp;
    $this->keylifetime = $keylifetime;
  }

  public function getConf()
  {
    $conf = "edit $this->name\n";
    $conf .= "set interface $this->if\n";
    $conf .= "set ike-version $this->ikeversion\n";
    $conf .= "set authmethod psk\n";
    $conf .= "set peertype any\n";
    if (in_array($this->auth, IPSec::$authEncAlgorithms)) {
      $conf .= "set proposal $this->auth\n";
    }
    $conf .= "set proposal $this->encryption-$this->auth\n";
    $conf .= "set dhgrp $this->dhgrp\n";
    $conf .= "set nattraversal $this->natt\n";
    $conf .= "set remote-gw $this->remotegw\n";
    $conf .= "set psksecret $this->psk\n";
    $conf .= "next\n";

    return $conf;
  }
}
