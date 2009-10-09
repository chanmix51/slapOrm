<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

class LdapCompareQueryOperator extends LdapBaseQueryOperator
{
  const EQUAL         = '=';
  const GREATER_THAN  = '>';
  const GREATER_EQUAL = '>=';
  const LESSER_THAN   = '<';
  const LESSER_EQUAL  = '<=';
  const EQUIVALENT    = '~=';

  protected $operator = self::EQUAL;

  public function __construct($ml, $mr, $operator = self::EQUAL)
  {
    parent::__construct($ml, $mr);
    $this->operator = $operator;
  }

  public function __toString()
  {
    return "(".$this->ml.$this->operator.$this->mr.")";
  }
}
