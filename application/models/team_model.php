<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Team_model extends CI_Model {
  /**
  *Table columns:
  *esportid int
  *name varchar 32
  *abbrv varchar 8
  *type varchar 16
  *description varchar 128
  *imageurl varchar 128
  */

  protected $table = 'teams';
  protected $pkey = 'teamid';

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function create_team($team,$captain)
  {
    $sql = "INSERT INTO teams (name, esportid, captainid, countryid, stateid, regionid) 
            VALUES ('". $team['name'] ."', '". $team['esportid'] ."', '". $captain['UserId'] ."', '". $captain['countryid'] ."', '". $captain['provincestateid'] ."', '". $captain['regionid'] ."')";
    $this->db1->query($sql);
    return;
  }
  public function get_team_by_id($teamid) {
    $sql = "SELECT * FROM teams WHERE teamid = '$teamid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_team_by_name($name,$esportid) {
    $sql = "SELECT * FROM teams WHERE name = '$name' AND esportid = '$esportid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_all_teams_by_captainid($id) {
    $sql = "SELECT * FROM teams WHERE captainid = '$id'";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_teams_by_captainid($id, $esportid) {
    $sql = "SELECT * FROM teams WHERE captainid = '$id' AND esportid = '$esportid'";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }
}