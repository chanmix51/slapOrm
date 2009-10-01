<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

class LdapEqualQueryOperator extends LdapBaseQueryOperator
{
  public function __toString()
  {
    return "($this->ml=$this->mr)";
  }
}
