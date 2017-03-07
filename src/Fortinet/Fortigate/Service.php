<?php

namespace Fortinet\Fortigate;

use Exception;
use Fortinet\Fortigate\Policy\PolicyService;

class Service extends PolicyService {

  const PROTO_ALL = "ALL";
  const PROTO_ICMP = "ICMP";
  const PROTO_IP = "IP";
  const PROTO_L4 = "TCP/UDP/SCTP";

  const L4_UDP = "udp";
  const L4_TCP = "tcp";

  private static $ALL = null;

  private $proto = self::PROTO_ALL;
  private $udpportrange = [];
  private $tcpportrange = [];

  public static function ALL()
  {
    if (!self::$ALL) {
      self::$ALL = new self("ALL");
    }
    return self::$ALL;
  }

  public function __construct($name, $proto = self::PROTO_ALL, $l4proto = "", $portrange = "")
  {
    $this->name = $name;
    $this->proto = $proto;
    if (!empty($portrange)) {
      $this->{$l4proto . "portrange"}[] = $portrange;
    }
  }

  public function addPortRange($l4proto, $portrange)
  {
    if (empty($portrange)) {
      throw new Exception("Portrange is empty", 1);
    }
    if ($l4proto == self::L4_TCP) {
      $this->tcpportrange[] = $portrange;
    }
    if ($l4proto == self::L4_UDP) {
      $this->udpportrange[] = $portrange;
    }
  }

  public function __get($property)
  {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function getConf()
  {
    $conf = "edit \"$this->name\"\n";
    $conf .= "set protocol $this->proto\n";
    if (($this->proto == self::PROTO_L4) && empty($this->udpportrange) && empty($this->tcpportrange)) {
      throw new Exception("There is no ports set", 1);
    }
    if (!empty($this->udpportrange)) {
      $conf .= "set udp-portrange " . implode(" ", $this->udpportrange) . "\n";
    }
    if (!empty($this->tcpportrange)) {
      $conf .= "set tcp-portrange " . implode(" ", $this->tcpportrange) . "\n";
    }
    $conf .= "next\n";

    return $conf;
  }
}
