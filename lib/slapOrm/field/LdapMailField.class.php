<?php

class LdapMailField extends LdapStringField
{
  protected $length = 256;

  public function getValidator()
  {
    $options = join(', ', $this->getOptionString());

    return "new sfValidatorEmail(array($options))";
  }
}
