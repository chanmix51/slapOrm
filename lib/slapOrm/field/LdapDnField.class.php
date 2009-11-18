<?php

class LdapDnField extends BaseLdapField
{
  public function getWidget()
  {
    return "new sfWidgetFormInput()";
  }

  public function getValidator()
  {
    $options = $this->getOptionString();
    $options[] = "'pattern' => '/\w+=[^,]+(,\w=[^,]+)*/i'";
    $options = join(', ', $options);

    return "new sfValidatorRegex(array($options))";
  }
}
