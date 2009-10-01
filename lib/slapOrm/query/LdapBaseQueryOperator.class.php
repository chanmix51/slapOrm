<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

abstract class LdapBaseQueryOperator
{
  protected $ml;
  protected $mr;

  public function __construct($ml, $mr)
  {
    $this->mr = $mr;
    $this->ml = $ml;
  }

  abstract public function __toString();
}
