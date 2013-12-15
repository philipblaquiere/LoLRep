<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_Roles_Model extends CI_Model{
  /**
  * uid int PK
  *  rid int PK
   */

	private $table = 'user_roles';

	public function __construct(){
    parent::__construct();
  }

 	public function factory(){
    $object->uid = null;
    $object->rid = null;
    return $object;
  }

  public function delete($entity) {
    $CI =& get_instance();
    $sql = "DELETE FROM  {$this->table} WHERE uid = :uid and rid = :rid";
    $bindings = array(':uid' => $entity->uid, ':rid' => $entity->rid);
    $CI->database_layer->query($sql, $bindings);
  }

  public function get_by_uid($uid) {
    $CI =& get_instance();
    $sql = "SELECT * FROM {$this->table} WHERE uid = :uid";
    $bindings = array(':uid' => $uid);
    $result = $CI->database_layer->query($sql, $bindings);
    $entity = $result->fetchAll();
    if (!$entity) {
      return FALSE;
    }
    return $entity;
  }

	public function get_by_rid($rid) {
    $CI =& get_instance();
    $sql = "SELECT * FROM {$this->table} WHERE rid = :rid";
    $bindings = array(':rid' => $rid);
    $result = $CI->database_layer->query($sql, $bindings);
    $entity = $result->fetchAll();
    if (!$entity) {
      return FALSE;
    }
    return $entity;
  }

  public function get_roles_by_uid($uid){
  	$CI =& get_instance();
  	$sql = "SELECT * FROM {$this->table} WHERE uid = :uid";
  	$bindings = array(':uid' => $uid);
  	$result = $CI->database_layer->query($sql, $bindings);
  	$entity =$result->fetchAll();
  	if(!$entity){
  		return FALSE;
  	}
  	return $entity;
  }

  public function get_users_by_rid($rid){
  	$CI =& get_instance();
  	$sql = "SELECT * FROM {$this->table} WHERE rid = :rid";
  	$bindings = array(':rid' => $rid);
  	$result = $CI->database_layer->query($sql, $bindings);
  	$entity =$result->fetchAll();
  	if(!$entity){
  		return FALSE;
  	}
  	return $entity;
  }

  public function save($entity) {
    $CI =& get_instance();
    if (isset($entity->uid) && isset($entity->rid)) {
      $bindings = array(':uid' => $entity->uid, ':rid' => $entity->rid);
      $sql = "INSERT INTO {$this->table} (uid, rid) VALUES (:uid, :rid)";
    }
    else {
      return FALSE;
    }
    $result = $CI->database_layer->query($sql, $bindings);
 		return $CI->database_layer->lastInsertId();
  }
}
