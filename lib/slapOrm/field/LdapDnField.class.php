<?php

class LdapDnField extends BaseLdapField
{
  public function __construct($multiple = false)
  {
    $this->type = 'dn';
    $this->multiple = $multiple;
  }

  public function getWidget()
  {
    return new sfWidgetFormInput();
  }

  public function getValidator()
  {
    return new sfValidatorRegex(array('pattern' => '/\w+=[^,]+(,\w=[^,]+)*/i'));
  }
}
