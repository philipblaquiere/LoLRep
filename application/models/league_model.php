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
      $sql = "INSERT INTO league_owners(leagueid, UserId)
              VALUES ('" . $uniqueid . "', '" . $_SESSION['user']['UserId'] . "')";
      $this->db1->query($sql);
  		$this->db1->trans_complete();
      return true;
  	}

  	public function get_league_types() {
  		$sql = "SELECT * FROM league_types";
	  	$result = $this->db1->query($sql);
		return $result->result_array();
  	}
}