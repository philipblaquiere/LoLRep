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

    switch ($team['esportid']) {
      case '1':
          //League of Legends
          $sql = "INSERT INTO teams_lol (teamid, summonerid)
                  VALUES (LAST_INSERT_ID() , '" . $captain['gameid']['SummonerId'] . "')";
          $this->db1->query($sql);
          return;
          break;
      case 2:
          break;
        }
    
  }
  public function get_teams_by_teamid($teamid) {
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


  public function get_teams_by_captainid($uid, $esportid) {
    $sql = "SELECT * FROM teams WHERE captainid = '$uid' AND esportid = '$esportid'";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_teams_by_esport($uid,$esportid) {

    switch ($esportid) {
      case 1:
        //ESport - League of Legends
        $sql = "SELECT * FROM teams t INNER JOIN teams_lol l ON t.teamid = l.teamid";
        $result = $this->db1->query($sql);
        return $result->result_array();
          break;
      case 2:
          break;
        }
  }
   public function get_all_teams_by_uid($uid) {
    /*returns 
      teamid
      name
      esportid
      created
      captainid
      countryid
      stateid
      regionid
      teamid
      summonerid
      joined_date
      UserId
      SummonerId
      SummonerName
      ProfileIconId
      RevisionDate
      SummonerLevel
      created*/
    $sql = "SELECT * FROM teams t
            INNER JOIN teams_lol l ON t.teamid = l.teamid 
            INNER JOIN summoners s ON s.summonerid = l.summonerid WHERE  s.UserId = '$uid'";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }
}