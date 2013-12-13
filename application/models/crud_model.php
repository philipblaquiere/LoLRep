<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud_Model extends CI_Model {
  protected $table = '';
  protected $pkey = '';

  public function __construct() {
    parent::__construct();
  }

  public function factory(){
    $CI =& get_instance();
    $sql = "DESCRIBE {$this->table}";
    $result = $CI->database_layer->query($sql);
    $table_fields = $result->fetchAll(PDO::FETCH_COLUMN);
    $object = new stdClass();
    foreach ($table_fields as $table_field){
      $object->{$table_field} = null;
    }
    return $object;
  }

  public function delete($id) {
    $CI =& get_instance();
    $sql = "DELETE FROM  {$this->table} WHERE {$this->pkey} = :id";
    $bindings = array(':id' => $id);
    $CI->database_layer->query($sql, $bindings);
  }

  public function get($id) {
    $CI =& get_instance();
    $sql = "SELECT * FROM {$this->table} WHERE {$this->pkey} = :id";
    $bindings = array(':id' => $id);
    $result = $CI->database_layer->query($sql, $bindings);
    $entity = $result->fetchObject();
    if (!$entity) {
      return FALSE;
    }
    return $entity;
  }

  public function get_all($order_by = null){
    $CI =& get_instance();
    $sql = "SELECT * FROM {$this->table}" . ($order_by ? ' ' . $order_by : '');
    $result = $CI->database_layer->query($sql);
    $entities = $result->fetchAll();
    if (!count($entities)) {
      return array();
    }
    return $entities;
  }

  public function save($entity) {
    $CI =& get_instance();
    if (isset($entity->{$this->pkey})) {
      $bindings = array();
      $toUpdate = array();
      foreach ($entity as $property => $value) {
        if ($property != $this->pkey) {
          $toUpdate[$property] = "$property = :$property";
        }
        $bindings[":$property"] = $entity->{$property};
      }
      $sql = sprintf("UPDATE {$this->table} SET %s WHERE %s = :%s", implode(', ', $toUpdate), $this->pkey, $this->pkey);
    }
    else {
      $properties = array();
      $values = array();
      foreach ($entity as $property => $value) {
        $bindings[":$property"] = $entity->{$property};
        // This doesn't need to be associative, but comes in handy below to grab a simple list of property names.
        $toInsert[$property] = ":$property";
      }
      $colNames = implode(', ', array_keys($toInsert));

      $sql = sprintf("INSERT INTO {$this->table} (%s) VALUES (%s)", $colNames, implode(', ', $toInsert));
    }
    $result = $CI->database_layer->query($sql, $bindings);
    return $result ? $CI->database_layer->lastInsertId() : FALSE;
  }
}
