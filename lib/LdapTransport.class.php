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

  abstract public function getAttributes();

  public function createQuery()
  {
    $query = new LdapQuery();
    $query->setAttributes($this->getAttributes());

    return $query;
  }

  public function getByCn($cn)
  {
    $slap_orm = SlapOrm::getInstance();

    $object = $slap_orm->getFromMap($cn.','.$this->base_dn);
    if (!$object)
    {
      $query = $this->createQuery()->setCn($cn)->setLimit(1);
      $result = $this->ldap_search($query);
      $object = count($result) == 1 ? $result[0] : null;
    }

    return $object;
  }

  public function __construct()
  {
    if (!$this->handler = @ldap_connect(sfConfig::get('app_ldap_host', 'localhost'), sfConfig::get('app_ldap_port', 389)))
    {
      throw new LdapTransportException(sprintf('Error while connecting to ldap host "%s", port "%s <br />LDAP said «%s»."',
        sfConfig::get('app_ldap_host'),
        sfConfig::get('app_ldap_port'),
        ldap_error($this->handler)));
    }

    ldap_set_option($this->handler, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (!@ldap_bind($this->handler, sfConfig::get('app_ldap_dn'), sfConfig::get('app_ldap_pass')))
    {
      throw new LdapTransportException(sprintf('Could not bind to LDAP tree dn="%s", server said «%s»', sfConfig::get('app_ldap_dn'), ldap_error($this->handler)));
    }
  }

  public function __destruct()
  {
    ldap_unbind($this->handler);
  }

  public function ldap_search(LdapQuery $query)
  {
    $dn = $query->getCn() !== '' ? $query->getCn().','.$this->base_dn : $this->base_dn;

    $res = ldap_search($this->handler, $dn, $query->getFilters(), $query->getAttributes(), 0, $query->getLimit());

    if ($res === false)
    {
      throw new LdapTransportException(sprintf('Error during the query "%s" on dn=«%s». <br />Ldap said «%s»', $query, $dn, ldap_error($this->handler)));
    }

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
      throw new LdapTransportException(sprintf('Could not add object'));
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
      throw new LdapTransportException(sprintf('Could not delete object'));
        return false;
    }
  }

  public function getHandler()
  {
    return $this->handler;
  }

}
