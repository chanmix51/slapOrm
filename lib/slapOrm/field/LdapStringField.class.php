<?php

class LdapStringField extends BaseLdapField
{
  protected $length;

  public function getLength()
  {
    return $this->length;
  }

  public function getWidget()
  {
    return $this->length < 256 ? new sfWidgetFormFilterInput() : new sfWidgetFormTextarea();
  }

  public function getValidator()
  {
    return new sfValidatorString(array('max_length' => $this->length));
  }
}
