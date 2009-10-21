<?php

class LdapTelField extends BaseLdapField
{
  public function __construct($multiple = false)
  {
    $this->type = 'tel';
    $this->multiple = $multiple;
  }

  public function getWidget()
  {
    return "new sfWidgetFormInput()";
  }

  public function getValidator()
  {
    return "new sfValidatorString()";
  }
}
