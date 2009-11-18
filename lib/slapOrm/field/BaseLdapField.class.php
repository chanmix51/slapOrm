<?php

abstract class BaseLdapField
{
  protected $type;
  protected $multiple = false;
  protected $required = false;

  public function __construct(Array $parameters)
  {
      if (!array_key_exists('type', $parameters))
      {
        throw new LdapException(sprintf('No type given for attribute "%s".', $field_name));
      }

      foreach($parameters as $parameter => $value)
      {
        $this->$parameter = $value;
      }
  }

  public function getType()
  {
    return $this->type;
  }

  public function getMultiple()
  {
    return $this->multiple;
  }

  public function isRequired()
  {
    return $this->required;
  }

  protected function getOptionString()
  {
    $required = $this->isRequired() ? "true" : "false";

    return array("'required' => $required");
  }

  abstract function getWidget();

  abstract function getValidator();
}
