<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

	public function create_matches($leagueid, $schedule) {
		
		$sql = "INSERT INTO matches (matchid, leagueid, teamaid, teambid, match_date) VALUES";
		foreach ($schedule as $match) {
			$uniqueid = $this->generate_unique_key();
			$sql .=	"('" . $uniqueid . "','" . $leagueid . "','" . $match['teamaid'] . "','" . $match['teambid'] . "','" . $match['match_date'] . "'),";
		}
		$sql = substr($sql, 0, -1);
		$this->db1->query($sql);
	}

	public function get_match_by_matchid($matchid, $esportid)
	{
		$sql = "SELECT * FROM matches
				WHERE matchid = '$matchid'
				LIMIT 1";
		$result = $this->db1->query($sql);
		return $result->row_array();
	}
	
	public function get_matches_by_team($team) {
		$season_start = $team['start_date'];
		$season_end = $team['end_date'];
		$teamid = $team['teamid'];

		$sql = "SELECT * FROM matches
				WHERE (teamaid = '$teamid' OR teambid = '$teamid')
				AND match_date > '$season_start' AND match_date < '$season_end'";
		$results = $this->db1->query($sql);
		$results = $results->result_array();
		$matches = array();
		foreach ($results as $result) {
			$match = array();
			$match['matchid'] = $result['matchid'];
			$match['leagueid'] = $result['leagueid'];
			$match['teamaid'] = $result['teamaid'];
			$match['teambid'] = $result['teambid'];
			$match['match_date'] = $this->get_local_datetime($result['match_date']);
			$match['winnerid'] = $result['winnerid'];
			$match['status'] = $result['status'];
			array_push($matches, $match);
		}
		return $matches;
	}

	public function get_matches_by_leagueid($leagueid, $season) {
		$season_start = $season['start_date'];
		$season_end = $season['end_date'];

		$sql = "SELECT * FROM matches
				WHERE leagueid = '$leagueid'
				AND match_date >= '$season_start' AND match_date < '$season_end'";
		$results = $this->db1->query($sql);
		$results = $results->result_array();
		$matches = array();
		foreach ($results as $result) {
			$match = array();
			$match['matchid'] = $result['matchid'];
			$match['leagueid'] = $result['leagueid'];
			$match['teamaid'] = $result['teamaid'];
			$match['teambid'] = $result['teambid'];
			$match['match_date'] = $this->get_local_datetime($result['match_date']);
			$match['winnerid'] = $result['winnerid'];
			$match['status'] = $result['status'];
			array_push($matches, $match);
		}
		return $matches;
	}
}