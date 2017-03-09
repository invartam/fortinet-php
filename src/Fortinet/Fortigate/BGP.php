<?php
namespace Fortinet\Fortigate;

use Fortinet\Fortigate\BGPNeighbor;

class BGP {

  private $routerId = "";
  private $AS = 0;
  private $neighbors = [];

  public function __construct($routerId, $AS)
  {
    if (count(explode(".", $routerId)) != 4) {
      throw new Exception("RouterID $routerId must be an IP address", 1);
    }
    if ($AS <= 0 || $AS >= 65535) {
      throw new Exception("AS number must be comprise between 0 and 65535", 1);
    }
    $this->routerId = $routerId;
    $this->AS = $AS;
  }

  public function addNeighbor(BGPNeighbor $neigh)
  {
    if (array_key_exists($neigh->getIP(), $this->neighbors)) {
      throw new Exception("BGP Neighbor " . $neigh->getIP() . " exists", 1);
    }
    $this->neighbors[$neigh->getIP()] = $neigh;
  }

  public function getRouterID()
  {
    return $this->routerId;
  }

  public function getAS()
  {
    return $this->as;
  }

  public function getNeighbors()
  {
    return $this->neighbors;
  }

  public function getConf()
  {
    $conf = "set as $this->AS\n";
    $conf .= "set router-id $this->routerId\n";
    if (!empty($this->neighbors)) {
      $conf .= "config neighbor\n";
      foreach ($this->neighbors as $neigh) {
        $conf .= $neigh->getConf();
      }
      $conf .= "end\n";
    }

    return $conf;
  }
}
