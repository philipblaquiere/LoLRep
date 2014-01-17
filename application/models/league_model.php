<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class League_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

  	public function get_league_by_name($leaguename) {
	  	$sql = "SELECT * FROM leagues
	  			WHERE name = '$leaguename'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		  return $result->row_array();
  	}

  	public function create_league($league) {
  		$uniqueid = $this->generate_unique_key();
      
      $this->db1->trans_start();
      
      $sql = "INSERT INTO leagues(leagueid, name, esportid, typeid, max_teams,invite, private)
          VALUES ('" . $uniqueid . "', '" . $league['name'] . "', '" . $league['esportid'] . "', '" . $league['typeid'] . "', '" . $league['max_teams'] . "', '" . $league['invite'] . "', '" . $league['privateleague'] . "')";
      $this->db1->query($sql);
      $sql = "INSERT INTO leagues_meta(leagueid, first_games, seasonid)
          VALUES";
      foreach ($league['leagues_meta'] as $leagues_meta) {
        $sql .= "('" . $uniqueid . "', '" . $leagues_meta . "', '" . $league['seasonid'] . "'),";
      }
      $sql = substr($sql, 0, -1);
      $this->db1->query($sql);
      $sql = "INSERT INTO league_owners(leagueid, UserId,seasonid,esportid)
              VALUES ('" . $uniqueid . "', '" . $_SESSION['user']['UserId'] . "', '" . $league['seasonid'] . "', '" . $league['esportid'] . "')";
      
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
    public function get_all_leagues_detailed($seasonid,$private = 0) {
      $sql = "SELECT * FROM leagues l
              INNER JOIN leagues_meta lm ON l.leagueid = lm.leagueid WHERE lm.seasonid = '$seasonid' AND l.private = '$private'";
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $league_info = array();
      foreach ($results as $result) {
        if(array_key_exists($result['name'], $league_info)) {
          //League array already created, add first game time only.
          array_push($league_info[$result['name']]['first_games'], $this->get_local_datetime($result['first_games']));
        }
        else {
          //Not in league array, create new league
          $league_info[$result['name']] = array();
          $league_info[$result['name']]['name'] = $result['name'];
          $league_info[$result['name']]['esportid'] = $result['esportid'];
          $league_info[$result['name']]['typeid'] = $result['typeid'];
          $league_info[$result['name']]['max_teams'] = $result['max_teams'];
          $league_info[$result['name']]['invite'] = $result['invite'];
          $league_info[$result['name']]['first_games'] = array();
          array_push($league_info[$result['name']]['first_games'], $this->get_local_datetime($result['first_games']));
        }
      }
      return $league_info;
    }
    public function get_active_league_teams($private = 0) {
      $sql = "SELECT * FROM leagues l 
              INNER JOIN league_teams lt WHERE l.leagueid = lt.leagueid AND lt.status = 'active' AND l.private = '$private'";
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $active_leagues = array();
      foreach ($results as $result) {
        if(array_key_exists($result['name'], $active_leagues)) {
          //League array already created, add first game time only.
          array_push($active_leagues[$result['name']]['teamid'], $result['teamid']);
        }
        else {
          //Not in league array, create new league
          $active_leagues[$result['name']] = array();
          $active_leagues[$result['name']]['leagueid'] = $result['leagueid'];
          $active_leagues[$result['name']]['teamid'] = array();
          array_push($active_leagues[$result['name']]['teamid'], $result['teamid']);
        }
      }
      //return $results->result_array();
      return $active_leagues;
    }
  	public function get_league_types() {
  		$sql = "SELECT * FROM league_types";
	  	$result = $this->db1->query($sql);
		  return $result->result_array();
  	}
    public function get_league_owner($uid, $seasonid) {
      $sql = "SELECT * FROM league_owners
              WHERE seasonid = '$seasonid' AND UserId = '$uid'";
      $result = $this->db1->query($sql);
      return $result->row_array();      
    }
}