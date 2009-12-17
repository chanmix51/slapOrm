<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

abstract class LdapBaseQueryOperator
{
  protected $members = array();

  abstract public function getOperator();

  public function __construct(Array $members)
  {
    $this->members = $members;
  }

  public function __toString()
  {
    return sprintf('(%s%s)', $this->getOperator(), join('', $this->members));
  }
}
