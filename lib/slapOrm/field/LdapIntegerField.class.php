<?php

class LdapIntegerField extends LdapStringField
{
  protected $length = 10;

  public function getValidator()
  {
    return "new sfValidatorInteger()";
  }
}
