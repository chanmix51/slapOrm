<?php

class SlapOrm
{
  protected static $instance;
  protected $data_map = array();

  public static function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function setIntoMap(LdapObject $object)
  {
    $this->data_map[$object->getDn()] = $object;
  }

  public function getFromMap($dn)
  {
    return array_key_exists($dn, $this->data_map) ? $this->data_map[$dn] : null;
  }

  public function existInMap($dn)
  {
    return array_key_exists($dn, $this->data_map);
  }
}
