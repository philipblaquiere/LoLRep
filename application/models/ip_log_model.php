<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ip_Log_Model extends CI_Model{
  /**
  *  ip int PK
  *  logged timestamp PK
   */

  private $table = 'ip_log';

  public function __construct(){
    parent::__construct();
  }

  public function get_by_ip($ip) {
    $CI =& get_instance();
    $sql = "SELECT * FROM {$this->table} WHERE ip = :ip and (unix_timestamp(logged) + " . IP_TTL . ") >= unix_timestamp(current_timestamp)";
    $bindings = array(':ip' => $ip);
    $result = $CI->database_layer->query($sql, $bindings);
    $entity = $result->fetchAll();
    if (!$entity) {
      return FALSE;
    }
    return $entity;
  }

  public function log_ip($ip) {
    $CI =& get_instance();
    if (isset($ip)) {
      $bindings = array(':ip' => $ip);
      $sql = "INSERT INTO {$this->table} (ip, logged) VALUES (:ip, current_timestamp)";
    }
    else {
      return FALSE;
    }
    $result = $CI->database_layer->query($sql, $bindings);
    return $CI->database_layer->lastInsertId();
  }
}
