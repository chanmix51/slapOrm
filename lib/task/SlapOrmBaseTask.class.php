<?php

abstract class SlapOrmBaseTask extends sfBaseTask
{
  protected function getSchema()
  {
    $schema_file = sfConfig::get('sf_config_dir').'/slapOrm/schema.yml';
    if (!file_exists($schema_file))
    {
      throw new sfCommandException(sprintf('No schema file found "%s"', $schema_file));
    }
    $this->log(sprintf('Found a schema file at "%s". Parsing the schema file', $schema_file));
    return sfYaml::load($schema_file);
  }

  protected function generateClassesFor($class_name)
  {
    if (!$this->filesAlreadyExistFor($class_name))
    {
      $this->generateUserClassesFor($class_name);
    }
    $this->deleteOldBaseClasses();
    $this->generateBaseClassesFor($class_name);
  }

  abstract protected function getPath();
  abstract protected function filesAlreadyExistFor($class_name);
  abstract protected function generateUserClassesFor($class_name);
  abstract protected function generateBaseClassesFor($class_name);

  protected function createFile($file_name, $code)
  {
    $file_name = $this->getPath().'/'.$file_name;
    $this->createLibDirIfNotExist();
    $this->log('+file '.$file_name);

    if (file_put_contents($file_name, $code) === false)
    {
      throw new sfCommandException(sprintf('Could not write to file "%s"', $file_name));
    }
  }

  protected function createLibDirIfNotExist()
  {
    $base_dir = $this->getPath();
    foreach(array($base_dir, $base_dir.'/base') as $dir)
    {
      if (!is_dir($dir))
      {
        $this->log('+dir '.$dir);
        if (!mkdir($dir, 0755, true))
        {
          throw new sfCommandException(sprintf('Could not create dir "%s"', $dir));
        }
      }
    }
  }

  protected function deleteOldBaseClasses()
  {
    # TODO
  }
}
