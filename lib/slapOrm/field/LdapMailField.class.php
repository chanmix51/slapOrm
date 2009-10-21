<?php

class LdapMailField extends LdapStringField
{
  protected $length = 256;

  public function getValidator()
  {
    return "new sfValidatorEmail()";
  }
}
