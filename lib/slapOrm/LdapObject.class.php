<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */
abstract class LdapObject implements ArrayAccess
{
  const EXIST    = 1;
  const MODIFIED = 2;

  protected $vars = array();
  protected $state = 0;
  protected $dn;

  public function getDn()
  {
    return $this->dn;
  }

  public function hydrateFromLdap(array $ldap_vars)
  {
    if (!array_key_exists('dn', $ldap_vars))
    {
      throw new LdapException('No dn to hydrate the object. Given values :<br /><pre>%s</pre>', print_r($ldap_vars, true));
    }
    $this->dn = $ldap_vars['dn'];
    unset($ldap_vars['dn']);

    if (!array_key_exists('count', $ldap_vars))
    {
      throw new LdapException(sprintf('No count given in the hydration vars. Given values :<br /><pre>%s</pre>', print_r($ldap_vars, true)));
    }

    foreach($this->extractAttributes($ldap_vars) as $attribute)
    {
      $this->$attribute = $this->extractValues($ldap_vars[$attribute]);
    }

    $this->state = self::EXIST;
  }

  private function extractAttributes($vars)
  {
    $attributes = array();
    for($i = 0; $i < $vars['count']; $i++)
    {
      $attributes[] = $vars[$i];
    }

    return $attributes;
  }

  private function extractValues($node)
  {
    if (!array_key_exists('count', $node))
    {
      throw new LdapException(sprintf('Could not find a count in the given node : <br /><pre>%s</pre>', print_r($node, true)));
    }
    if ($node['count'] == 1)
    {
      return $node[0];
    }

    return $this->extractAttributes($node);
  }

  public function fromArray(array $array)
  {
    $this->vars = $array;
    $this->modify();

    return $this->vars;
  }

  public function toArray()
  {
    return $this->vars;
  }

  public function offsetExists($offset)
  {
    return isset($this->vars[$offset]);
  }

  public function offsetGet($offset)
  {
    return $this->__get($offset);
  }

  public function offsetSet($offset, $value)
  {
    $this->__set($offset, $value);
  }

  public function offsetUnset($offset)
  {
    unset($this->vars[$offset]);
  }

  public function __set($key, $value)
  {
    $this->vars[$key] = $value;
  }

  public function __get($key)
  {
    return array_key_exists($key, $this->vars) ? $this->vars[$key] : null;
  }

  public function __call($method, $arguments)
  {
    $operation = substr(strtolower($method), 0, 3);
    $attribute = sfInflector::underscore(substr($method, 3));

    switch($operation)
    {
    case 'set':
      return $this->__set($attribute, $arguments[0]);
      break;
    case 'get':
      return $this->__get($attribute);
      break;
    default:
      throw new LdapException(sprintf('No such method "%s:%s()"', get_class($this), $method));
    }
  }

  protected function modify()
  {
    if (!$this->state && self::MODIFIED)
    {
      $this->state = $this->state + self::MODIFIED;
    }
  }

  public function exists()
  {
    return $this->state && self::EXIST;
  }

  public function modified()
  {
    return $this->state && self::MODIFIED;
  }

  public function save()
  {
    if (!$this->modified())
    {
      return true;
    }
  }

  public function debug()
  {
    throw new LdapException(sprintf('<br /><pre>%s</pre>', print_r($this, true)));
  }
}
