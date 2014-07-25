<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

	public function get_recent_matches($playerid, $)

	public function create_matches($leagueid, $schedule) {
		
		$sql = "INSERT INTO matches (matchid, leagueid, teamaid, teambid, match_date) VALUES";
		foreach ($schedule as $match) {
			$uniqueid = $this->generate_unique_key();
			$sql .=	"('" . $uniqueid . "','" . $leagueid . "','" . $match['teamaid'] . "','" . $match['teambid'] . "','" . $match['match_date'] . "'),";
		}
		$sql = substr($sql, 0, -1);
		$this->db1->query($sql);
	}

	public function get_match($matchid, $esportid)
	{
		$sql = "SELECT * FROM matches
				WHERE matchid = '$matchid'
				LIMIT 1";
		$result = $this->db1->query($sql);
		return $result->row_array();
	}

	public function get_matches($matchids, $esportid)
	{
		switch ($esportid) {
			case '1':
				$matches = array();
				$this->db1->trans_start();
				foreach ($matchids as $matchid) {
					$sql = "SELECT sl.*, s.SummonerName, s.SummonerId FROM statistics_lol sl
						INNER JOIN summoners s ON s.SummonerId = sl.SummonerId
						WHERE sl.matchid = '$matchid'";
					$statistics_results = $this->db1->query($sql);
      				$statistics_results = $statistics_results->result_array();
      				
      				//Clean up stats
      				$statistics = array();
      				foreach ($statistics_results as $statistics_result)
      				{
      					$statistics[$statistics_result['summonerid']] = $statistics_result;
      				}

      				//Get all summonerids in match stats
      				$summonerids = "";
      				foreach ($statistics_lol as $player)
      				{
      					$summonerids .= $player['summonerid'] . ",";
      				}
      				$summonerids = "(" . substr($summonerids, 0, -1) . ")";

      				//Query from summonerids to get both teams info
					$sql = "SELECT s.SummonerName, t.team_name, t.teamid , m.* FROM summoners s 
						INNER JOIN matches m ON m.matchid = '$matchid'
						INNER JOIN teams t ON t.team_name = m.teamaid OR t.team_name = m.teambid
						WHERE s.SummmonerId IN '$summonerids'";
					$player_teams = $this->db1->query($sql);
      				$player_teams = $player_teams->result_array();

      				$teama = array();
      				$teamb = array();
      				$match_details = array();
      				foreach ($player_teams as $player_team)
      				{
      					$match_details['match_date'] = $player['match_date'];
      					$match_details['winnerid'] = $player['winnerid'];
      					$match_details['status'] = $player['status'];
      					$player = array();
      					$team_name = $player_team['team_name'];
      					$player['name'] = $player_team['SummonerName'];
      					$player['statistics'] = $statistics[$player['SummonerId']];
      					if(array_key_exists('name', $teama) && $teama['name'] == $team_name)
      					{
      						//player part of teama
      						array_push($teama, $player);
      					}
      					else if (array_key_exists('name', $teamb) && $teamb['name'] == $team_name)
      					{
      						//player part of teamb
      						array_push($teamb, $player);
      					}
      					else if(!array_key_exists('name', $teama) && !array_key_exists('name', $teamb))
      					{
      						//initialize teama 
      						$teama['name'] = $player_team['team_name'];
      						$teama['teamid'] = $player_team['teamid '];
      					}
      					else if(!array_key_exists('name', $teamb))
      					{
      						//initialize teamb
      						$teamb['name'] = $player_team['team_name'];
      						$teamb['teamid'] = $player_team['teamid'];
      					}
      					else
      					{
      						//code will never reach here
      					}
      				}
      				$match['match_date'] = $match_details['match_date'];
      				$match['winnerid'] = $match_details['winnerid'];
      				$match['status'] = $match_details['status'];
      				$match = array();
      				array_push($match, $teama);
      				array_push($match, $teamb);
      				$match['details'] = $match;
      				array_push($matches, $match);
				}
				$this->db1->trans_complete();
				return $matches;
				break;
			
			default:
				# code...
				break;
		}
	}
	
	public function get_matches_by_team($team) {
		$season_start = $team['season']['start_date'];
		$season_end = $team['season']['end_date'];
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