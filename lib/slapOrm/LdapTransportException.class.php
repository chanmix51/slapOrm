<?php
/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */

class LdapTransportException extends Exception
{
  public function __construct($handler, $message)
  {
    $this->message = sprintf("%s\nLast LDAP message was -->%s<--", $message, ldap_error($handler));
  }
}
