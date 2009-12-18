<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

class LdapQuery 
{
  protected $filters;
  protected $limit       = 0;
  protected $attributes  = array();
  protected $objectClass = '';

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

  public function createFilter(LdapBaseQueryOperator $operator)
  {
    $this->filters = $operator;

    return $this;
  }

  public function addAndFilter(LdapBaseQueryOperator $operator)
  {
    $this->filters = new LdapAndQueryOperator(array($this->filters, $operator));

    return $this;
  }

  public function addOrFilter(LdapBaseQueryOperator $operator)
  {
    $this->filters = new LdapOrQueryOperator(array($this->filters, $operator));

    return $this;
  }

  public function getFilters()
  {
    return (string)$this->filters;
  }

  public function __toString()
  {
    return sprintf('filters=«%s», limit=«%d»', $this->getFilters(), $this->getLimit());
  }
}
