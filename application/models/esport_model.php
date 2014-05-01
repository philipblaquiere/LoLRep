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
  public function get_esport_byid($esportid) {
    $sql = "SELECT * FROM esports
              WHERE esportid = '$esportid'
              LIMIT 1";
    $result = $this->db1->query($sql);
    return  $result->row_array();
  }
  public function get_all_esports() {
    $sql = "SELECT * FROM esports";
    $result = $this->db1->query($sql);
    $results = $result->result_array();
    $esports = array();

    foreach ($results as $result) {
      $esports[$result['esportid']] = array();
      $esports[$result['esportid']]['esportid'] = $result['esportid'];
      $esports[$result['esportid']]['esport_name'] = $result['esport_name'];
      $esports[$result['esportid']]['abbrv'] = $result['abbrv'];
      $esports[$result['esportid']]['esport_type'] = $result['esport_type'];
      $esports[$result['esportid']]['esport_description'] = $result['esport_description'];
      $esports[$result['esportid']]['imageurl'] = $result['imageurl'];
      $esports[$result['esportid']]['min_players'] = $result['min_players'];
      $esports[$result['esportid']]['max_players'] = $result['max_players'];
    }
    return $esports;
  }

  public function get_all_registered_esports($uid) {

      $sql = "SELECT e.esportid AS esportid, e.esport_name AS esport_name FROM esports e 
              INNER JOIN user_esport u ON e.esportid = u.esportid 
              WHERE u.UserId = '$uid'";
      $result = $this->db1->query($sql);
      return $result->result_array();
  }
}