<?php
/* Copyright 2009 Grégoire HUBERT
 *
 * This is free software
 * See the LICENCE file
 */
class LdapResult implements arrayAccess, iterator, countable
{
  protected $result = array();
  protected $elt_count;
  protected $transport;

  public function __construct(array $result_set = array(), LdapTransport $transport)
  {
    $this->transport = $transport;
    $class = $this->transport->getClassName();

    foreach ($result_set as $offset => $result)
    {
      if ( $offset === 'count' )
      {
        $this->elt_count = $result;
      }
      else
      {
        $object = new $class();
        $object->hydrate($result);
        $this->result[] = $object;
      }
    }
  }

  public function offsetExists($offset)
  {
    return isset($this->result[$offset]);
  }

  public function offsetGet($offset)
  {
    return $this->result[$offset];
  }

  public function offsetSet($offset, $value)
  {
    throw new Exception('Can not write any value in a result var');
  }

  public function offsetUnset($offset)
  {
    throw new Exception('Can not Delete any results');
  }

  public function rewind()
  {
    reset($this->result);
  }

  public function current()
  {
    return current($this->result);
  }

  public function key()
  {
    return key($this->result);
  }

  public function next()
  {
    return next($this->result);
  }

  public function valid()
  {
    return $this->current() !== false;
  }

  public function count()
  {
    return $this->elt_count;
  }
}
