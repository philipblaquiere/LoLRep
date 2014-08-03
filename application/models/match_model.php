<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}


	public function create_matches($leagueid, $schedule)
	{
		$sql = "INSERT INTO matches (matchid, leagueid, teamaid, teambid, match_date) VALUES";
		foreach ($schedule as $match) {
			$uniqueid = $this->generate_unique_key();
			$sql .=	"('" . $uniqueid . "','" . $leagueid . "','" . $match['teamaid'] . "','" . $match['teambid'] . "','" . $match['match_date'] . "'),";
		}
		$sql = substr($sql, 0, -1);
		$this->db1->query($sql);
	}

	public function update_matches($matches)
	{
		//check for non-array
		if(array_key_exists('matchid', $matches))
		{
			$matches = array($matches);
		}

		$sql = "INSERT INTO matches
				VALUES ";
		foreach ($matches as $match)
		{
			$matchid = $match['matchid'];
			$gameid = $match['gameid'];
			$leagueid = $match['leagueid'];
			$teamaid = $match['teama']['teamaid'];
			$teambid = $match['teamb']['teambid'];
			$match_date = $match['match_date'];
			$winnerid = $match['winnerid'];
			$status = 'finished';
			$sql .= "('" . $matchid. "','" .$gameid. "','" .$leagueid. "','".$teamaid. "','".$teambid."','".$match_date."','".$winnerid."','".$status."'),";
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ON DUPLICATE KEY UPDATE gameid=VALUES(gameid), winnerid=VALUES(winnerid), status=VALUES(status);";
		$this->db1->query($sql);
		return;
	}

	public function get_scheduled_matches($teamids, $time_now)
	{
		$sqla = "SELECT m.matchid
				FROM matches AS m
				WHERE m.match_date < '$time_now'
					AND m.status = 'scheduled'
					AND (m.gameid = '' OR m.gameid IS NULL)
					AND (m.teamaid IN ('" . implode("','", $teamids) . "'))";
		$sqlb = "SELECT m.matchid
				FROM matches AS m
				WHERE m.match_date < '$time_now'
					AND m.status = 'scheduled'
					AND (m.gameid = '' OR m.gameid IS NULL)
					AND (m.teambid IN ('" . implode("','", $teamids) . "'))";
		$this->db1->trans_start();
		$resulta = $this->db1->query($sqla);
		$resultb = $this->db1->query($sqlb);
		$this->db1->trans_complete();
		$matchidsa = $resulta->result_array();
		$matchidsb = $resultb->result_array();
		
		$matchids = array();
		foreach ($matchidsa as $matchida)
		{
			array_push($matchids, $matchida['matchid']);
		}
		foreach ($matchidsb as $matchidb)
		{
			array_push($matchids, $matchidb['matchid']);
		}
		return $matchids;
	}

	public function get_matches($matchids, $esportid)
	{
		$sql = "SELECT 	m.matchid,
						m.gameid,
						m.match_date,
						m.winnerid,
						m.status,
						t.teamid,
						t.team_name,
						s.seasonid,
						s.start_date,
						s.end_date,
						s.season_status,
						l.leagueid,
						l.league_name,
						l.league_type,
						l.invite,
						l.private,
						l.imageurl,
						l.league_status
				FROM matches AS m, teams AS t, leagues AS l, league_teams AS lt, seasons AS s, season_leagues AS sl
				WHERE (m.teamaid = t.teamid OR m.teambid = t.teamid) 
					AND m.leagueid = l.leagueid
					AND sl.leagueid = l.leagueid
					AND s.seasonid = sl.seasonid
					AND l.esportid = '$esportid'
					AND lt.leagueid = m.leagueid
					AND lt.teamid = t.teamid
					AND m.matchid IN ('" . implode("','", $matchids) . "')";
		$this->db1->trans_start();
		$result = $this->db1->query($sql);
		$match_results = $result->result_array();


		$matches = array();

		//used to get players in the teams in next sql call
		$teamids = array();

		foreach ($match_results as $match)
		{
			if(!in_array($match['teamid'], $teamids))
			{
				array_push($teamids, $match['teamid']);
			}

			if(array_key_exists($match['matchid'], $matches))
			{

				$matches[$match['matchid']]['teamb']['teambid'] = $match['teamid'];
				$matches[$match['matchid']]['teamb']['team_name'] = $match['team_name'];
			}
			else
			{
				$temp_match = $match;
				$temp_match['matchid'] = $match['matchid'];
				$temp_match['match_date'] = $match['match_date'];
				$temp_match['status'] = $match['status'];
				$temp_match['leagueid'] = $match['leagueid'];
				$temp_match['league_name'] = $match['league_name'];
				$temp_match['league_type'] = $match['league_type'];
				$temp_match['invite'] = $match['invite'];
				$temp_match['private'] = $match['private'];
				$temp_match['imageurl'] = $match['imageurl'];
				$temp_match['league_status'] = $match['league_status'];
				$temp_match['teama']['teamaid'] = $match['teamid'];
				$temp_match['teama']['team_name'] = $match['team_name'];
				unset($temp_match['teamid']);
				unset($temp_match['team_name']);
				$temp_match['complete'] = !($temp_match['gameid'] == NULL || $temp_match['gameid'] =='');
				$matches[$match['matchid']] = $temp_match;
			}
		}
		$players = array();

		$sql = "SELECT 	p.playerid,
						p.player_name,
						pt.teamid
				FROM players AS p, player_teams pt
				WHERE p.playerid = pt.playerid
					AND pt.teamid IN ('" . implode("','", $teamids) . "')";

		$result = $this->db1->query($sql);
		$this->db1->trans_complete();
		$players_result = $result->result_array();
		$players = array();
		foreach ($players_result as $player)
		{
			$teamid = $player['teamid'];
			unset($player['teamid']);
			$players[$teamid] = array();
			//array_push($players[$teamid], $player);
			$players[$teamid][$player['playerid']] = $player;
		}

		foreach ($matches as &$match)
		{
			$match['teama']['roster'] = $players[$match['teama']['teamaid']];
			$match['teamb']['roster'] = $players[$match['teamb']['teambid']];
			

			if($match['complete'] == 1)
			{
				//Match is complete, pull player stats from the correct table
				$match['teama']['teama_players'] = array();
				$match['teamb']['teamb_players'] = array();
				switch ($esportid)
				{
					case '1':
						$player_stats = $this->_get_lol_player_stats($match['matchid']);
						
					default:
						foreach ($player_stats as $playerid => $player)
						{
							if(array_key_exists($playerid, $match['teama']['roster']))
							{
								$match['teama']['teama_players'][$playerid] = $player;
							}
							else
							{
								$match['teamb']['teamb_players'][$playerid] = $player;
							}
						}
						break;
				}
			}
		}
		return $matches;
	}
	
	private function _get_lol_player_stats($matchid)
	{
		$sql = "SELECT * FROM lol_statistics
					WHERE matchid = '$matchid'";
		$results = $this->db1->query($sql);
		$result = $results->result_array();
		foreach ($result as $player_stats)
		{
			$temp_stats = array();
			$temp_stats['teamId'] = $player_stats['teamId'];
			$temp_stats['summmonerId'] = $player_stats['playerid'];
			$temp_stats['championId'] = $player_stats['championId'];
			$temp_stats['spell1'] = $player_stats['spell1'];
			$temp_stats['spell2'] = $player_stats['spell2'];
			$temp_stats['level'] = $player_stats['level'];
			unset($player_stats['teamId']);
			unset($player_stats['playerid']);
			unset($player_stats['championId']);
			unset($player_stats['spell1']);
			unset($player_stats['spell2']);
			$stats = array();
			$stats['win'] = $player_stats['win'];
			$stats['assists'] = $player_stats['assists'];
			$stats['championsKilled'] = $player_stats['championsKilled'];
			$stats['numDeaths'] = $player_stats['numDeaths'];
			$stats['minionsKilled'] = $player_stats['minionsKilled'];
			unset($player_stats['win']);
			unset($player_stats['assists']);
			unset($player_stats['championsKilled']);
			unset($player_stats['numDeaths']);
			unset($player_stats['minionsKilled']);
			foreach ($player_stats as $key => $value)
			{
				if($value != 0 && $value != '0' && $value != NULL)
				{
					$stats[$key] = $value;
				}
			}
			$temp_stats['stats'] = $stats;
			$players[$temp_stats['summmonerId']] = $temp_stats;
		}
		return $players;
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