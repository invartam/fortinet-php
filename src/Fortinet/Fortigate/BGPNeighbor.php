<?php
namespace Fortinet\Fortigate;

class BGPNeighbor {

  private $IP = "0.0.0.0";
  private $AS = 0;
  private $password = "";

  public function __construct($ip, $as, $password = "")
  {
    if (count(explode(".", $ip)) != 4) {
      throw new Exception("RouterID $ip must be an IP address", 1);
    }
    if ($as <= 0 || $as >= 65535) {
      throw new Exception("AS number must be comprise between 0 and 65535", 1);
    }
    $this->IP = $ip;
    $this->AS = $as;
    $this->password;
  }

  public function getIP()
  {
    return $this->IP;
  }

  public function getAS()
  {
    return $this->AS;
  }

  public function getConf()
  {
    $conf = "edit $this->IP\n";
    $conf .= "set remote-as $this->AS\n";
    if (!empty($this->password)) {
      $conf .= "set password $this->password\n";
    }
    $conf .= "next\n";

    return $conf;
  }
}
