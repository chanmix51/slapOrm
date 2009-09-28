<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

class LdapQuery 
{
  protected $cn = '';
  protected $filters = '';
  protected $limit = 0;
  protected $attributes = array();

  public function setCn($cn)
  {
    $this->cn = $cn;

    return $this;
  }

  public function getCn()
  {
    return $this->cn;
  }

  public function setLimit($limit = 0)
  {
    $this->limit = $limit;

    return $this;
  }

  public function getLimit()
  {
    return $this->limit;
  }

  public function setAttributes(array $attributes)
  {
    $this->attributes = $attributes;

    return $this;
  }

  public function getAttributes()
  {
    return $this->attributes;
  }

  public function setFilters($filters)
  {
    $this->filters = $filters;

    return $this;
  }

  public function getFilters()
  {
    return $this->filters;
  }

  public function __toString()
  {
    return sprintf('cn=«%s», filters=«%s», limit=«%d»', $this->getCn(), $this->getFilters(), $this->getLimit());
  }
}
