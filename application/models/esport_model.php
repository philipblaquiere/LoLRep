<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Esport_model extends CI_Model {
  /**
  *Table columns:
  *esportid int
  *name varchar 32
  *abbrv varchar 8
  *type varchar 16
  *description varchar 128
  *imageurl varchar 128
  */

  protected $table = 'esports';
  protected $pkey = 'esportid';

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function get_all_esports() {
    $sql = "SELECT * FROM esports";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function get_all_registered_esports($uid) {

      $sql = "SELECT e.esportid AS esportid, e.name AS name FROM esports e INNER JOIN user_esport u ON e.esportid = u.esportid WHERE u.UserId = '$uid'";
      $result = $this->db1->query($sql);
      return $result->result_array();
  }

  public function register_user_lol($uid) {
    $sql = "INSERT INTO user_esport (UserId, esportid) 
            VALUES ('" . $uid . "', '1')";
    $result = $this->db1->query($sql);
  }
}