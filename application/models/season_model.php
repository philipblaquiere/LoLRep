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

  	public function get_current_season($teamid) {
  		$sql = "SELECT s.start_date as start_date, s.season_esportid as season_esportid, s.season_status as season_status, s.season_status as season_status, s.created as created FROM seasons s
	  			INNER JOIN season_leagues sl ON s.seasonid = sl.seasonid
	  			INNER JOIN league_teams lt ON lt.leagueid = sl.leagueid
	  			WHERE lt.teamid = '$teamid'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		return $result->row_array();
  	}


  	public function create_season($season) {
  		$uniqueid = $this->generate_unique_key();
  		$sql = "INSERT INTO seasons (seasonid, owner_UserId, season_duration)
  				VALUES ('" . $uniqueid . "', '" . $season['UserId'] . "', '" . $season['season_duration'] . "')";
  		$this->db1->query($sql);
  	}

  	public function start_season($seasonid, $start_date, $end_date) {
  		$sql = "UPDATE seasons
  			SET season_status = 'active', start_date = '$start_date', end_date = '$end_date'
  			WHERE seasonid = '$seasonid'";
  		$this->db1->query($sql);
  	}

  	public function get_seasons_by_owner($userid, $esportid) {
  		$sql = "SELECT * FROM seasons
  				WHERE owner_userid = '$userid'
  				AND season_status = 'active' OR season_status = 'new'
          AND season_esportid = '$esportid'
  				LIMIT 1";
  		$result = $this->db1->query($sql);
		return $result->row_array();
  	}
}