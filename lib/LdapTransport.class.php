<?php
/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

abstract class LdapTransport
{
  protected $handler;
  protected $base_dn;

  abstract public function getClassName();

  public function getByDn($node)
  {
    $dn = $this->base_dn.','.$node;

    return $this->ldap_search($dn);
  }

  public function __construct()
  {
    if (!$this->handler = ldap_connect(sfConfig::get('app_ldap_host', 'localhost'), sfConfig::get('app_ldap_port', 389)))
    {
      throw new Exception(sprintf('Error while connecting to ldap host "%s", port "%s"',
        sfConfig::get('app_ldap_host'),
        sfConfig::get('app_ldap_port')));
    }

    ldap_set_option($this->handler, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (!ldap_bind($this->handler, sfConfig::get('app_ldap_dn'), sfConfig::get('app_ldap_pass')))
    {
      throw new Exception(sprintf('Could not bind to LDAP tree dn="%s", server said «%s»', sfConfig::get('app_ldap_dn'), ldap_error()));
    }
  }

  public function __destruct()
  {
    ldap_unbind($this->handler);
  }

  public function ldap_search($filters = '', $attributes = array())
  {
    $res = ldap_search($this->handler, $this->base_dn, $filters, $attributes);

    return new LdapResult(ldap_get_entries($this->handler, $res), $this);
  }

  public function ldap_modify($dn = LdapObject, $entry = array())
  {
    $this->res = ldap_modify($this->handler, $dn, $entry);
    
    return (ldap_error($this->handler) == "Success");
  }

  public function ldap_add($dn = LdapObject, $entry = array())
  {
    $this->res = ldap_add($this->handler, $dn, $entry);
    
    if(ldap_error($this->handler) == "Success")
        return true;
    else 
    {
      throw new Exception(sprintf('Could not add object'));
        return false;
    }
  }

  public function ldap_delete($dn = LdapObject)
  {
    $this->res = ldap_delete($this->handler, $dn);
    
    if(ldap_error($this->handler) == "Success")
        return true;
    else 
    {
      throw new Exception(sprintf('Could not delete object'));
        return false;
    }
  }

  public function getHandler()
  {
    return $this->handler;
  }

}
