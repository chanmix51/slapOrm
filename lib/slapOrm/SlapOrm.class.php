<?php

class SlapOrm
{
  const VERSION='Alpha-4';

  protected static $instance;
  protected $data_map = array();
  protected $connections = array();

  public static function getMapInstanceOf($model_class)
  {
    $model_class = $model_class.'Map';

    return new $model_class;
  }

  public function __destruct()
  {
    foreach ($this->connections as $connection)
    {
      ldap_unbind($connection);
    }
  }

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

  public function getConnectionFor($classname)
  {
    return array_key_exists($classname, $this->connections) ? $this->connections[$classname] : null;
  }

  public function setConnectionFor($classname, $handler)
  {
    $this->connections[$classname] = $handler;
  }
}
