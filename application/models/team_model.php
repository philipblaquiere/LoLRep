<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Team_model extends MY_Model {
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
    $uniqueid = $this->generate_unique_key();
    $team['name'] = $this->make_mysql_friendly($team['name']);
    switch ($team['esportid']) {
      case '1':
          //League of Legends
          $sql = "INSERT INTO teams_lol (teamid, summonerid)
                  VALUES ('". $uniqueid ."' , '" . $captain['gameid']['SummonerId'] . "')";
          $min_players = '5';
          $this->db1->query($sql);
          break;
      case 2:
          break;
        }
    $sql = "INSERT INTO teams (teamid,team_name, esportid, captainid, countryid, stateid, regionid) 
          VALUES ('". $uniqueid ."','". $team['name'] ."', '". $team['esportid'] ."', '". $captain['UserId'] ."', '". $captain['countryid'] ."', '". $captain['provincestateid'] ."', '". $captain['regionid'] ."')";
    $this->db1->query($sql);

    
  }
  public function get_team_by_teamid($teamid) {
    $time_now = $this->get_default_epoch(date('Y-m-d H:i:s', time()));
    $sql = "SELECT * FROM teams t
          INNER JOIN league_teams lt ON lt.teamid = t.teamid
          INNER JOIN leagues l ON l.leagueid = lt.leagueid
          INNER JOIN season_leagues sl ON sl.leagueid = lt.leagueid
          INNER JOIN seasons s ON s.seasonid = sl.seasonid
          WHERE t.teamid = '$teamid' AND s.season_status != 'ended'
          LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_teamname_by_teamid($teamid) {
    $sql = "SELECT team_name FROM teams WHERE teamid = '$teamid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_team_by_name($name,$esportid) {
    $sql = "SELECT * FROM teams WHERE team_name = '$name' AND esportid = '$esportid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_all_teams_by_captainid($id) {
    $sql = "SELECT * FROM teams WHERE captainid = '$id'";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_team_by_captainid($uid, $esportid) {
    $sql = "SELECT * FROM teams WHERE captainid = '$uid' AND esportid = '$esportid' LIMIT 1";
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

  public function get_team_id_by_summonername($summonername) {
    $sql = "SELECT t.teamid as teamid FROM teams t
        INNER JOIN teams_lol l ON t.teamid = l.teamid 
        INNER JOIN summoners s ON s.summonerid = l.summonerid WHERE  s.SummonerName = '$summonername' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_lol_teamname_by_uid($uid) {
    $sql = "SELECT t.team_name as team_name FROM teams t
            INNER JOIN teams_lol l ON t.teamid = l.teamid 
            INNER JOIN summoners s ON s.summonerid = l.summonerid WHERE  s.UserId = '$uid'";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function get_team_by_uid($uid, $esportid) {
    switch ($esportid) {
      case '1':
        //League of Legends
        $sql = "SELECT * FROM teams t
              INNER JOIN teams_lol l ON t.teamid = l.teamid 
              INNER JOIN summoners s ON s.summonerid = l.summonerid 
              WHERE s.UserId = '$uid'
              LIMIT 1";
        $result = $this->db1->query($sql);
        return $result->row_array();
        break;

      default:
        # code...
        break;
    }
    
  }

   public function get_all_teams_by_uid($uid, $esportid) {
    /*returns 
      teamid
      team_name
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
      switch ($esportid) {
        case '1':
        //League of Legends
          $sql = "SELECT * FROM teams t
            INNER JOIN teams_lol l ON t.teamid = l.teamid 
            INNER JOIN summoners s ON s.summonerid = l.summonerid WHERE s.UserId = '$uid'";
          break;
        
        default:
          # code...
          break;
      }
    
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function get_team_lol_byname($teamname) {
    $sql = "SELECT s.UserId as UserId, s.SummonerId as SummonerId, s.SummonerName as SummonerName FROM summoners s
            INNER JOIN teams t ON t.team_name = '$teamname' 
            INNER JOIN teams_lol l ON l.teamid = t.teamid
            WHERE l.summonerid = s.SummonerId";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }
  public function get_team_roster($teamid, $esportid) {
    switch ($esportid) {
      case '1':
        $sql = "SELECT * FROM summoners s
            INNER JOIN teams_lol t ON t.teamid = '$teamid'
            WHERE t.summonerid = s.SummonerId AND t.status != 'inactive'";
        break;
    }
    
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function add_summoner_to_team($teamid, $summonerid) {
    $sql = "INSERT INTO teams_lol (teamid, summonerid)
            VALUES ('" . $teamid . "', '" . $summonerid . "')";
    $this->db1->query($sql);
  }

  /*
  * Removes summoner from the team
  * which he/she is active
  */
  public function remove_summoner_from_team($summonerid) {
    $sql = "UPDATE team_lol
              SET status='leave', leave_date = now()
              WHERE summonerid = '$summonerid' AND status = 'active'";
    $this->db1->query($sql);
  }

  public function get_teams_byleagueid($leagueid, $esportid) {
    $sql = "SELECT * FROM leagues l 
              INNER JOIN league_teams lt ON l.leagueid = lt.leagueid 
              INNER JOIN teams t ON t.teamid = lt.teamid 
              WHERE l.leagueid = '$leagueid' AND t.esportid = '$esportid' AND lt.status = 'active' AND l.private = '0'";
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $league = array();
      $teams = array();
      foreach ($results as $result) {
        if(array_key_exists('teams', $teams)) {
          //League array already created, add team
          $league['teams'][$result['teamid']] = array();
          $league['teams'][$result['teamid']]['teamid'] = $result['teamid'];
          $league['teams'][$result['teamid']]['joined'] = $result['joined'];
          $league['teams'][$result['teamid']]['team_name'] = $result['team_name'];
          $league['teams'][$result['teamid']]['captainid'] = $result['captainid'];
        }
        else {
          //Not in league array, create new league
          $league['league_name'] = $result['league_name'];
          $league['leagueid'] = $result['leagueid'];
          $league['teams'][$result['teamid']] = array();
          $league['teams'][$result['teamid']]['teamid'] = $result['teamid'];
          $league['teams'][$result['teamid']]['joined'] = $result['joined'];
          $league['teams'][$result['teamid']]['team_name'] = $result['team_name'];
          $league['teams'][$result['teamid']]['captainid'] = $result['captainid'];
        }
      }
      return $league;
  }
}

