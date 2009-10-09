<?php

class SlapOrmTask extends sfBaseTask
{
  protected $schema;

  protected function configure()
  {
    $this->namespace        = 'slaporm';
    $this->name             = 'build-model';
    $this->briefDescription = 'generate LDAP model classes based on the schema definition';
    $this->detailedDescription = <<<EOF
The [slaporm::build-model|INFO] generates your model class files based on the definition given in config/slapOrm/schema.yml

Call it with:

  [php symfony slaporm::build-model|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $schema_file = sfConfig::get('sf_config_dir').'/slapOrm/schema.yml';
    if (!file_exists($schema_file))
    {
      throw new sfCommandException(sprintf('No schema file found "%s"', $schema_file));
    }
    $this->log(sprintf('Found a schema file at "%s". Parsing the schema file', $schema_file));
    $this->schema = sfYaml::load($schema_file);

    foreach(array_keys($this->schema) as $class)
    {
      $this->generateModelClassesFor($class);
    }
  }

  protected function generateModelClassesFor($class_name)
  {
    if (!$this->filesAlreadyExistFor($class_name))
    {
      $this->generateUserClassesFor($class_name);
    }
    $this->generateBaseClassesFor($class_name);
  }

  protected function filesAlreadyExistFor($class_name)
  {
    return
      (
        file_exists(sfConfig::get('sf_lib_dir').'/model/slapOrm/'.$class_name.'.class.php')
      and
        file_exists(sfConfig::get('sf_lib_dir').'/model/slapOrm/'.$class_name.'Map.class.php')
      );
  }

  public function generateUserClassesFor($class_name)
  {
    $this->log(sprintf('Creating user model files for class "%s"', $class_name));
    $version = SlapOrm::VERSION;
    $code = <<<EOF
<?php
 /*
  * SlapOrm version $version
  */
class ${class_name} extends LdapObject
{
}
EOF;
    $this->createFile($class_name.'.class.php', $code);
    $code =<<<EOF
<?php
 /*
  * SlapOrm version $version
  */
class ${class_name}Map extends Base${class_name}Map
{
}
EOF;
    $this->createFile($class_name.'Map.class.php', $code);
  }

  protected function generateBaseClassesFor($class_name)
  {
    $this->log(sprintf('Creating base model file for class "%s"', $class_name));
    $attributes = '\''.join('\', \'', array_keys($this->schema[$class_name]['attributes'])).'\'';
    $fields = $this->generateFieldsCode($class_name);
    $object_class = $this->schema[$class_name]['objectClass'];
    $dn = $this->schema[$class_name]['dn'];
    $version = SlapOrm::VERSION;
    $code = <<<EOF
<?php
  /*
 * This file has been automatically generated by SlapOrm
 * DO NOT EDIT THIS FILE as the next slaporm::build-model
 * will overwrite this file again
 *
 * SlapOrm version $version
 */
class Base${class_name}Map extends LdapTransport
{
  protected \$base_dn = "$dn";
  protected \$attributes = array($attributes);
  protected \$object_class = '$object_class';

  public function configure()
  {
$fields
  }

  public function getClassName()
  {
    return '${class_name}';
  }
}
EOF;
    $this->createFile('base/Base'.$class_name.'Map.class.php', $code);
  }

  protected function createFile($file_name, $code)
  {
    $file_name = sfConfig::get('sf_lib_dir').'/model/slapOrm/'.$file_name;
    $this->createLibDirIfNotExist();
    $this->log('+file '.$file_name);

    if (file_put_contents($file_name, $code) === false)
    {
      throw new sfCommandException(sprintf('Could not write to file "%s"', $file_name));
    }
  }

  protected function createLibDirIfNotExist()
  {
    $base_dir = sfConfig::get('sf_lib_dir').'/model/slapOrm';
    foreach(array($base_dir, $base_dir.'/base') as $dir)
    {
      if (!is_dir($dir))
      {
        $this->log('+dir '.$dir);
        if (!mkdir($dir))
        {
          throw new sfCommandException(sprintf('Could not create dir "%s"', $dir));
        }
      }
    }
  }

  protected function generateFieldsCode($class_name)
  {
    $fields = "";
    foreach ($this->schema[$class_name]['attributes'] as $field_name => $parameters)
    {
      $fields .= sprintf('    $this->fields[\'%s\'] = new Ldap%sField(%s);', strtolower($field_name), sfInflector::camelize($parameters['type']), $this->convertArrayInPhp($parameters));
      $fields .= "\n";
    }

    return $fields;
  }

  protected function convertArrayInPhp(array $array)
  {
    $fields = "array(";
    foreach($array as $key => $value)
    {
      switch(strtolower(gettype($value)))
      {
      case "boolean":
        $value = $value ? 'true' : 'false';
      case "integer":
        $fields .= "'$key' => $value,";
        break;
      default:
        $fields .= "'$key' => '$value',";
      }
    }
    $fields .= ')';

    return $fields;
  }
}
