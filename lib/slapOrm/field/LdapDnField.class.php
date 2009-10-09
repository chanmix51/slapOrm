<?php

class LdapDnField extends BaseLdapField
{
  public function getWidget()
  {
    return new sfWidgetFormInput();
  }

  public function getValidator()
  {
    return new sfValidatorRegex(array('pattern' => '/\w+=[^,]+(,\w=[^,]+)*/i'));
  }
}
