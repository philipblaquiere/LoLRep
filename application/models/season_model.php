<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Season_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

  	public function get_season_by_name($seasonname) {
	  	$sql = "SELECT * FROM seasons
	  			WHERE name = '$seasonname' 
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function get_season_by_seasonid($sid) {
	  	$sql = "SELECT * FROM seasons
	  			WHERE seasonid = '$sid'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function get_season_dates($sid) {
	  	$sql = "SELECT startdate as startdate, enddate as enddate FROM seasons
	  			WHERE seasonid = '$sid' 
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function get_current_seasonid() {
  		$sql = "SELECT seasonid as seasonid FROM seasons
	  			WHERE status = 'active'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function get_new_season() {
  		$sql = "SELECT * FROM seasons
	  			WHERE status = 'new'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function get_open_season() {
  		$sql = "SELECT * FROM seasons
	  			WHERE status = 'open' 
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}

  	public function create_season($season) {
  		$uniqueid = $this->generate_unique_key();
  		$sql = "INSERT INTO seasons (seasonid, owner_UserId, registration_start,registration_end, startdate, enddate, name)
  				VALUES ('" . $uniqueid . "', '" . $season['UserId'] . "', '" . $season['registration_start'] . "', '" . $season['registration_end'] . "', '" . $season['startdate'] . "', '" . $season['enddate'] . "', '" . $season['name'] . "')";
  		$this->db1->query($sql);
  	}

  	public function open_season($seasonid) {
  		$sql = "UPDATE seasons SET status = 'open' WHERE seasonid = '$seasonid'";
  		$this->db1->query($sql);
  	}
}