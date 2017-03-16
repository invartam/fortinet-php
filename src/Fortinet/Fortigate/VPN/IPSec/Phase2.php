<?php
namespace Fortinet\Fortigate\VPN\IPSec;

use Fortinet\Fortigate\VPN\IPSec\IPSec;
use Fortinet\Fortigate\VPN\IPSec\Phase1;
use Fortinet\Fortigate\Policy\PolicyObject;

class Phase2 extends PolicyObject {

  private $p1 = null;
  private $auth = "sha256";
  private $enc = "aes128";
  private $dhgrp = 14;
  private $auto = "disable";
  private $keylifeseconds = 43200;
  private $leftSubnet = "0.0.0.0/0";
  private $rightSubnet = "0.0.0.0/0";

  public function __construct(Phase1 $p1, $left = "0.0.0.0/0", $right = "0.0.0.0/0", $enc = "aes128", $auth = "sha256", $dhgrp = 14, $keylifeseconds=43200, $auto = "disable")
  {
    if (!isset($p1)) {
      throw new Exception("You must provide a non empty IPSec phase 1", 1);
    }
    $this->p1 = $p1;
    $this->name = $p1->getName();
    if (count(explode("/", $left)) == 2 && count(explode(".", explode("/", $left)[0])) != 4) {
      throw new Exception("Left IP address incorrect", 1);
    }
    $this->leftSubnet = $left;
    if (count(explode("/", $right)) == 2 && count(explode(".", explode("/", $right)[0])) != 4) {
      throw new Exception("Right IP address incorrect", 1);
    }
    $this->rightSubnet = $right;
    if (!in_array($auth, IPSec::$authAlgorithms) && !in_array($auth, IPSec::$authEncAlgorithms)) {
      throw new Exception("Authentication algorithm $auth is not supported", 1);
    }
    $this->auth = $auth;
    if (!in_array($enc, IPSec::$encAlgorithms)) {
      throw new Exception("Encryption algorithm $enc is not supported", 1);
    }
    if (in_array($auth, IPSec::$authEncAlgorithms)) {
      $this->enc = $auth;
    }
    else {
      $this->enc = $enc;
    }
    if (!in_array($dhgrp, IPSec::$dhGroups)) {
      throw new Exception("DH Group $dhgrp not supported", 1);    
    }
    $this->dhgrp = $dhgrp;
    $this->keylifeseconds = $keylifeseconds;
    if ($auto != "disable" && $auto != "enable") {
      throw new Exception("Auto value not supported, please enter enable or disable", 1);
    }
    $this->auto = $auto;
  }

  public function getConf()
  {
    $conf = "edit $this->name\n";
    $conf .= "set phase1name $this->p1\n";
    if (in_array($this->auth, IPSec::$authEncAlgorithms)) {
      $conf .= "set proposal $this->auth\n";
    }
    else {
      $conf .= "set proposal $this->enc-$this->auth\n";
    }
    $conf .= "set dhgrp $this->dhgrp\n";
    $conf .= "set auto-negotiate $this->auto\n";
    $conf .= "set keylifeseconds $this->keylifeseconds\n";
    $conf .= "set src-subnet $this->leftSubnet\n";
    $conf .= "set dst-subnet $this->rightSubnet\n";
    $conf .= "next\n";

    return $conf;
  }
}