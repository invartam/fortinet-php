<?php
namespace Fortigate\Fortinet

class IPPool extends PolicyNat {

  private $type = "";
  private $ipl = "";
  private $iph = "";

  public function __construct($name, $type, $ipl, $iph = "")
  {
    $this->name = $name;
    $this->type = strtolower($type);
    $this->ipl = $ipl;
    $this->iph = $iph;
  }

  public function getConf()
  {
    if (!in_array($this->type, ["one-to-one", "overload"])) {
      throw new Exception("The IPPool type $this->type is not yet supported", 1);
    }
    if (empty($this->name) || empty($this->ipl)) {
      throw new Exception("Name and or IP Low cannot be empty", 1);
    }
    $conf = "edit $this->name\n";
    $conf .= "set type $this->type\n";
    $conf .= "set startip $this->ipl\n";
    if (empty($this->iph)) {
      $conf .= "set endip $this->ipl\n";
    }
    else {
      $conf .= "set endip $this->iph\n";
    }

    return $conf;
  }
}
