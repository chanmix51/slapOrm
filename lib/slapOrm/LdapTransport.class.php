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
  protected $fields;
  protected $attributes = array();

  abstract public function getClassName();

  abstract public function getRdnField();

  public function configure()
  {
  }

  public function createQuery()
  {
    $query = new LdapQuery();
    $query->setAttributes($this->getAttributes());
    $operators = array();
    foreach($this->object_class as $object_class)
    {
      $operators[] = new LdapCompareQueryOperator('objectClass', $object_class);
    }
    $query->createFilter(new LdapAndQueryOperator($operators));

    return $query;
  }

  public function getByRdn($rdn)
  {
    $slap_orm = SlapOrm::getInstance();

    $object = $slap_orm->getFromMap(sprintf('%s=%s,%s',$this->getRdnField(), $rdn, $this->base_dn));
    if (!$object)
    {
      $query = $this->createQuery()->createFilter(new LdapCompareQueryOperator($this->getRdnField(), $rdn))->setLimit(1);
      $result = $this->ldap_search($query);
      $object = count($result) == 1 ? $result[0] : null;
    }

    return $object;
  }

  protected function openConnection()
  {
    if (!$this->handler = @ldap_connect(sfConfig::get('app_ldap_host', 'localhost'), sfConfig::get('app_ldap_port', 389)))
    {
      throw new LdapTransportException($this->handler, sprintf('Error while connecting to ldap host "%s", port "%s".',
        sfConfig::get('app_ldap_host'),
        sfConfig::get('app_ldap_port')
        ));
    }
  }

  public function bindToLdap()
  {
    ldap_set_option($this->handler, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (!@ldap_bind($this->handler, sfConfig::get('app_ldap_dn'), sfConfig::get('app_ldap_pass')))
    {
      throw new LdapTransportException($this->handler, sprintf('Could not bind to LDAP tree dn="%s".', sfConfig::get('app_ldap_dn')));
    }
  }

  public function connect()
  {
    if (is_null($this->handler))
    {
      $this->handler = SlapOrm::getInstance()->getConnectionFor($this->getClassName());

      if (is_null($this->handler))
      {
        $this->openConnection();
        SlapOrm::getInstance()->setConnectionFor($this->getClassName(), $this->handler);
        $this->bindToLdap();
      }
    }
  }

  public function __construct()
  {
    $this->configure();
  }

  public function ldap_search(LdapQuery $query)
  {
    $this->connect();
    $res = @ldap_search($this->handler, $this->base_dn, $query->getFilters(), $query->getAttributes(), 0, $query->getLimit());

    if ($res === false)
    {
      throw new LdapTransportException($this->handler, sprintf('Error during the query "%s" on dn=«%s».', $query, $this->base_dn));
    }

    return new LdapResult(ldap_get_entries($this->handler, $res), $this);
  }

  public function ldap_modify(LdapObject $object)
  {
    $this->connect();
    if (!@ldap_modify($this->handler, $object->getDn(), $object->toArray()))
    {
      throw new LdapTransportException($this->handler, sprintf('Could not modify existing LDAP entry dn="%s"', $object->getDn()));
    }
  }

  public function ldap_add(LdapObject $object)
  {
    $this->connect();
    $dn = sprintf('%s=%s,%s', $this->getRdnField(), $object->get($this->getRdnField()), $this->base_dn);
    $values = array_merge(array('objectClass' => $this->object_class), $object->toArray());
    if (!@ldap_add($this->handler, $dn, $values))
    {
      throw new LdapTransportException($this->handler, sprintf('Could not add object dn="%s"', $dn));
    }
  }

  public function delete(LdapObject $object)
  {
    $this->connect();
    if (!@ldap_delete($this->handler, $object->getDn()))
    {
      throw new LdapTransportException($this->handler, sprintf('Could not delete object dn="%s".', $object->getDn()));
    }
  }

  public function getHandler()
  {
    return $this->handler;
  }

  public function getAttributes()
  {
    return $this->attributes;
  }

  public function getField($name)
  {
    if (array_key_exists($name, $this->fields))
    {
      return $this->fields[$name];
    }
    return null;
  }

  public function getFields()
  {
    return $this->fields;
  }

  public function findAll()
  {
    return $this->ldap_search($this->createQuery());
  }

  public function fetchOne($query)
  {
    $query->setLimit(1);
    $results = $this->ldap_search($query);

    if ($results->count())
    {
      return $results[0];
    }
  }

  public function save(LdapObject $object)
  {
    if ($object->exists())
    {
      $this->ldap_modify($object);
    }
    else
    {
      $this->ldap_add($object);
    }
  }
}
