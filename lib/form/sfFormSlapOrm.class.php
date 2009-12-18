<?php

abstract class sfFormSlapOrm extends sfForm
{
  protected $object;

  abstract protected function getModelName();

  public function __construct(LdapObject $object = null, $options = array(), $CSRFSecret = null)
  {
    $class_name = $this->getModelName();
    if (is_null($object))
    {
      $this->object = new $class_name();
    }
    else
    {
      if (!get_class($object) == $class_name)
      {
        throw new sfExceptions(sprintf('"%s" forms only accept object class "%s" as argument, object type "%s" given.', get_class($this), $class_name, get_class($object)));
      }
      $this->object = $object;
    }

    parent::__construct(array(), $options, $CSRFSecret);

    $this->updateDefaultsFromObject();
  }

  protected function updateDefaultsFromObject()
  {
    $this->setDefaults($this->getObject()->toArray());
  }

  public function bind(Array $tainted_values = null, Array $tainted_files = null)
  {
    parent::bind($tainted_values, $tainted_files);

    if ($this->isValid())
    {
      $this->processValues();
      $class_name = $this->getModelName();
      $map_object = SlapOrm::getMapInstanceOf($this->getModelName());
      $rdn_field = $map_object->getRdnField();

      $object = $map_object->getByRdn($this->getValue($map_object->getRdnField()));
      if (!$object)
      {
        $object = new $class_name();
      }
      $object->fromArray($this->getValues());

      $this->object = $object;
    }
  }

  public function bindAndSave(Array $tainted_values = null, Array $tainted_files = null)
  {
    $this->bind($tainted_values, $tainted_files);

    if ($this->isValid())
    {
      $this->object->save();

      return true;
    }

    return false;
  }

  public function getObject()
  {
    return $this->object;
  }

  public function processValues()
  {
    foreach ($this->values as $field_name => $value)
    {
      $method = sprintf('process%sValue', sfInflector::camelize($field_name));

      if (method_exists($this, $method))
      {
        if (NULL == $return_code = $this->$method($value))
        {
          unset($this->values[$field_name]);
        }
        else
        {
          $this->values[$field_name] = $return_code;
        }
      }
    }
  }
}
