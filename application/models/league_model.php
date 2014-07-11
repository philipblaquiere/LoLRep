<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class League_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

    public function get_league_byid($leagueid) {
      $sql = "SELECT * FROM leagues
          WHERE leagueid = '$leagueid'
          LIMIT 1";
      $result = $this->db1->query($sql);
      return $result->row_array();
    }
  	public function get_league_by_name($leaguename) {
      $leaguename = $this->make_mysql_friendly($leaguename);
	  	$sql = "SELECT * FROM leagues
	  			WHERE league_name = '$leaguename'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		  return $result->row_array();
  	}

  	public function create_league($league, $season) {
      $season_uniqueid = $this->generate_unique_key();
  		$league_uniqueid = $this->generate_unique_key();
      $league['name'] = $this->make_mysql_friendly($league['name']);

      $this->db1->trans_start();
      
      $sql = "INSERT INTO seasons (seasonid, owner_userid, season_duration, season_esportid)
          VALUES ('" . $season_uniqueid . "', '" . $season['userid'] . "', '" . $season['season_duration'] . "', '" . $season['season_esportid'] . "')";
      $this->db1->query($sql);

      $sql = "INSERT INTO season_leagues (seasonid, leagueid)
              VALUES ('" . $season_uniqueid . "', '" . $league_uniqueid . "')";
      $this->db1->query($sql);

      $sql = "INSERT INTO leagues(leagueid, league_name, esportid, league_type, max_teams,invite, private)
          VALUES ('" . $league_uniqueid . "', '" . $league['name'] . "', '" . $league['esportid'] . "', '" . $league['typeid'] . "', '" . $league['max_teams'] . "', '" . $league['invite'] . "', '" . $league['privateleague'] . "')";
      $this->db1->query($sql);

      $sql = "INSERT INTO league_meta(leagueid, first_matches, seasonid)
          VALUES";
      foreach ($league['leagues_meta'] as $leagues_meta) {
        $sql .= "('" . $league_uniqueid . "', '" . $leagues_meta . "', '" . $season_uniqueid . "'),";
      }
      $sql = substr($sql, 0, -1);
      $this->db1->query($sql);
      $sql = "INSERT INTO league_owners(leagueid, userid, seasonid, esportid)
              VALUES ('" . $league_uniqueid . "', '" . $season['userid'] . "', '" . $season_uniqueid . "', '" . $league['esportid'] . "')";
      
      $this->db1->query($sql);
  		$this->db1->trans_complete();
      return true;
  	}

    public function get_league_byesport($esportid) {
      $sql = "SELECT * FROM leagues
              WHERE esportid='$esportid'";
      $result = $this->db1->query($sql);
      return $result->result_array();
    }

    public function get_leagues_fromsearch($searchcriteria) {
      if(!$searchcriteria['search_text']) {

      }
      if(!$searchcriteria['esportid']) {

      }
      if(!$searchcriteria['typeid']) {

      }
      if(!$searchcriteria['max_teams']) {

      }
      if(!$searchcriteria['invite']) {

      }      
      if(!$searchcriteria['private']) {

      }
      if(!$searchcriteria['status']) {

      }    
    }
    public function get_all_leagues() {
      $sql = "SELECT * FROM leagues";
      $result = $this->db1->query($sql);
      return $result->result_array();
    }
    public function get_all_leagues_detailed($esportid,$private = 0) {
      $sql = "SELECT * FROM leagues l
              INNER JOIN leagues_meta lm ON l.leagueid = lm.leagueid 
              INNER JOIN league_types tp ON l.league_type = tp.league_type_id
              INNER JOIN esports e ON e.esportid = '$esportid'
              WHERE l.private = '$private' AND l.esportid = '$esportid'";
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $league_info = array();
      foreach ($results as $result) {
        if(array_key_exists($result['league_name'], $league_info)) {
          //League array already created, add first game time only.
          array_push($league_info[$result['league_name']]['first_matches'], $this->get_local_datetime($result['first_matches']));
        }
        else {
          //Not in league array, create new league
          $league_info[$result['league_name']] = array();
          $league_info[$result['league_name']]['leagueid'] = $result['leagueid'];
          $league_info[$result['league_name']]['league_name'] = $result['league_name'];
          $league_info[$result['league_name']]['esportid'] = $result['esportid'];
          $league_info[$result['league_name']]['esport_name'] = $result['esport_name'];
          $league_info[$result['league_name']]['league_type'] = $result['league_type'];
          $league_info[$result['league_name']]['max_teams'] = $result['max_teams'];
          $league_info[$result['league_name']]['invite'] = $result['invite'];
          $league_info[$result['league_name']]['first_matches'] = array();
          array_push($league_info[$result['league_name']]['first_matches'], $this->get_local_datetime($result['first_matches']));
        }
      }
      return $league_info;
    }

    /*
    *Returns all league and their active teams
    */
    public function get_active_league_teams($esportid)
    {
      $sql = "SELECT * FROM leagues l
              INNER JOIN league_teams lt ON l.leagueid = lt.leagueid 
              INNER JOIN teams t ON t.teamid = lt.teamid 
              WHERE t.esportid = '$esportid' AND lt.status = 'active' AND l.private = '0'";
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $active_leagues = array();
      foreach ($results as $result) {
        if(array_key_exists($result['league_name'], $active_leagues)) {
          //League array already created, add first game time only.
          $active_leagues[$result['league_name']]['teams'][$result['team_name']] = array();
          $active_leagues[$result['league_name']]['teams'][$result['team_name']]['teamid'] = $result['teamid'];
          $active_leagues[$result['league_name']]['teams'][$result['team_name']]['joined'] = $result['joined'];
        }
        else {
          //Not in league array, create new league
          $active_leagues[$result['league_name']] = array();
          $active_leagues[$result['league_name']]['leagueid'] = $result['leagueid'];
          $active_leagues[$result['league_name']]['teams'] = array();
          $active_leagues[$result['league_name']]['teams'][$result['team_name']] = array();
          $active_leagues[$result['league_name']]['teams'][$result['team_name']]['teamid'] = $result['teamid'];
          $active_leagues[$result['league_name']]['teams'][$result['team_name']]['joined'] = $result['joined'];
        }
      }
      return $active_leagues;
    }
    public function get_league_details($leagueid)
    {
      $sql =  "SELECT l.*, sl.*, s.*, lt.*, e.*, lm.first_matches FROM leagues AS l
              INNER JOIN season_leagues AS sl ON sl.leagueid = l.leagueid
              INNER JOIN seasons AS s ON s.seasonid = sl.seasonid
              INNER JOIN league_types AS lt ON l.league_type = lt.league_type_id
              INNER JOIN esports AS e ON l.esportid = e.esportid
              INNER JOIN leagues_meta AS lm ON lm.leagueid = l.leagueid
              WHERE l.leagueid = '$leagueid'";
      $results = $this->db1->query($sql);
      $results = $results->result_array();
      $league = $results[0];
      $league['first_matches'] = array();
      foreach ($results as $result) {
        array_push($league['first_matches'], $result['first_matches']);
      }
      return $league;
    }

    public function get_league_types()
    {
  		$sql = "SELECT * FROM league_types";
	  	$result = $this->db1->query($sql);
		  return $result->result_array();
  	}

    public function get_league_owner($uid, $seasonid)
    {
      $sql = "SELECT * FROM league_owners
              WHERE seasonid = '$seasonid' AND UserId = '$uid'";
      $result = $this->db1->query($sql);
      return $result->row_array();      
    }

    public function get_current_league_by_teamid($teamid)
    {
       $sql = "SELECT * FROM league_teams
              WHERE teamid = '$teamid' AND status = 'active'
              LIMIT 1";
      $result = $this->db1->query($sql);
      return $result->row_array();      
    }

    public function get_league_by_uid($uid, $esportid)
    {
      switch ($esportid) {
        case '1':
          //League of Legends
          $sql = "SELECT * FROM leagues l
              INNER JOIN summoners s ON s.UserId = '$uid'
              INNER JOIN teams_lol tl ON tl.summonerid = s.summonerid
              INNER JOIN teams t ON t.teamid = tl.teamid
              INNER JOIN league_teams lt ON lt.teamid = t.teamid
              WHERE l.leagueid = lt.leagueid and l.league_status != 'inactive'
              LIMIT 1";
          $result = $this->db1->query($sql);
          return $result->row_array();
          break;
        
        default:
          # code...
          break;
      }
    }

    public function get_active_league_first_matches($leagueid)
    {
      $sql = "SELECT lm.first_matches as first_matches FROM leagues_meta lm
              INNER JOIN leagues l ON l.leagueid = lm.leagueid
              WHERE lm.leagueid = '$leagueid' AND l.status != 'inactive'";
      $result = $this->db1->query($sql);
      $result = $result->result_array(); 
      $first_matches = array();
      foreach ($result as $match)
      {
        array_push($first_matches, $match['first_matches']);
      } 
      return $first_matches;    
    }

    public function join_league($teamid, $leagueid)
    {
      $sql = "INSERT INTO league_teams(teamid, leagueid)
              VALUES ('" . $teamid . "', '" . $leagueid . "')";
      $result = $this->db1->query($sql);
    }

    public function leave_league($teamid, $leagueid)
    {
      $sql = "UPDATE league_teams
              SET status='leave'
              WHERE teamid = '$teamid' AND leagueid = '$leagueid' AND status = 'active'";
      $this->db1->query($sql);
    }
}