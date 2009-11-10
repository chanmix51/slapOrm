<?php

/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

class LdapOrQueryOperator extends LdapBaseQueryOperator
{
  public function getOperator()
  {
    return '|';
  }
}

