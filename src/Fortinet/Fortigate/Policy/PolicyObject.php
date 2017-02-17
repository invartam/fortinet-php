<?php

namespace Fortinet\Fortigate\Policy;

class PolicyObject {

  protected $name = "";

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function __toString()
  {
    return $this->name;
  }
}
