<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class League_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

  	public function get_league_by_name($leaguename,$seasonid) {
	  	$sql = "SELECT * FROM leagues
	  			WHERE name = '$leaguename' 
	  			AND seasonid = '$seasonid' 
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function create_league($league) {
  		$uniqueid = $this->generate_unique_key();
  		$sql = "INSERT INTO leagues (leagueid, seasonid, name, esportid, type)
  				VALUES ('" . $uniqueid . "', '" . $league['seasonid'] . "', '" . $league['name'] . "', '" . $league['esportid'] . "', '" . $league['type'] . "')";
  		$this->db1->query($sql);
  	}
}