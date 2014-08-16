<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match_model extends MY_Model 
{
	const LOL_IMAGE_URL = "ddragon.leagueoflegends.com/cdn/4.13.1/img/sprite/";
	const LOL_MAX_MATCH_DURATION = 8800; // 2:30hrs
	const LOL_MIN_MATCH_DURATION = 1200; // 20 minutes
	const DATE_FORMAT = "DATE_RSS";
	const MATCH_FINISHED = 'finished';
	const MATCH_SCHEDULED = 'scheduled';

	public function __construct()
	{
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
		$this->load->library('lol_image_formatter');
	}


	public function create_matches($leagueid, $seasonid, $schedule)
	{
		$sql = "INSERT INTO matches (matchid, leagueid, seasonid, teamaid, teambid, match_date) VALUES";
		foreach ($schedule as $match) {
			$uniqueid = $this->generate_unique_key();
			$sql .=	"('" . $uniqueid . "','" . $leagueid . "','" . $seasonid . "','" . $match['teamaid'] . "','" . $match['teambid'] . "','" . $match['match_date'] . "'),";
		}
		$sql = substr($sql, 0, -1);
		$this->db1->query($sql);
		return;
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
			$seasonid = $match['seasonid'];
			$teamaid = $match['teama']['teamaid'];
			$teambid = $match['teamb']['teambid'];
			$match_date = $match['match_date'];
			$winnerid = $match['winnerid'];
			$status = 'finished';
			$sql .= "('" . $matchid. "','" .$gameid. "','" .$leagueid. "','".$seasonid. "','".$teamaid. "','".$teambid."','".$match_date."','".$winnerid."','".$status."'),";
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ON DUPLICATE KEY UPDATE gameid=VALUES(gameid), winnerid=VALUES(winnerid), status=VALUES(status);";
		$this->db1->query($sql);
		return;
	}

	public function get_scheduled_matches($teamids, $time_now)
	{
		$time_now = $this->local_to_gmt(intval($time_now), FALSE);
		$sqla = "SELECT m.matchid
				FROM matches AS m
				WHERE m.match_date < '$time_now'
					AND m.status = 'scheduled'
					AND (m.gameid = '' OR m.gameid IS NULL)
					AND (m.teamaid IN ('" . implode("','", $teamids) . "'))
				ORDER BY m.match_date DESC";
		$sqlb = "SELECT m.matchid
				FROM matches AS m
				WHERE m.match_date < '$time_now'
					AND m.status = 'scheduled'
					AND (m.gameid = '' OR m.gameid IS NULL)
					AND (m.teambid IN ('" . implode("','", $teamids) . "'))
				ORDER BY m.match_date DESC";
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

	public function get_scheduled_matchids($time_now, $esportid)
	{
		$time_now = $this->local_to_gmt(intval($time_now), FALSE);
		$min_match_duration = $this->_get_min_match_length($esportid);
		$max_match_duration = $this->_get_max_match_length($esportid);
		$time_max_duration = $time_now - $max_match_duration;

		$sql = "SELECT m.matchid
				FROM matches m, leagues l
				WHERE m.leagueid = l.leagueid
					AND l.esportid = '$esportid'
					AND m.match_date <= '$time_max_duration'";
		$result = $this->db1->query($sql);
		$matchids_array = $result->result_array();
		$matchids = array();
		foreach ($matchids_array as $matchid)
		{
			array_push($matchids, $matchid['matchid']);
		}
		return $matchids;
	}

	public function get_upcoming_matchids($playerid, $esportid)
	{
		$time_delay = $this->local_to_gmt(time(), FALSE) -  $this->_get_max_match_length($esportid);
		$sql = "SELECT m.matchid
				FROM matches m, players p, player_teams pt, league_teams lt, leagues l
				WHERE p.playerid = '$playerid'
					AND p.playerid = pt.playerid
					AND pt.teamid = lt.teamid
					AND l.leagueid = lt.leagueid
					AND l.esportid = '$esportid'
					AND lt.leagueid = m.leagueid
					AND (m.teamaid = pt.teamid OR m.teambid = pt.teamid)
					AND m.status = 'scheduled'
					AND m.match_date > '$time_delay'
				ORDER BY m.match_date";
		$result =$this->db1->query($sql);
		$matchids_array = $result->result_array();
		$matchids = array();
		foreach ($matchids_array as $matchid)
		{
			array_push($matchids, $matchid['matchid']);
		}
		return $matchids;
	}

	public function get_upcoming_matchids_byteam($teamid, $esportid)
	{
		$time_now = $this->local_to_gmt(intval(time()), FALSE);
		$sql = "SELECT m.matchid
				FROM matches m, players p, player_teams pt, league_teams lt, leagues l
				WHERE (m.teamaid = '$teamid' OR m.teambid = '$teamid')
					AND m.status = 'scheduled'
					AND m.match_date > '$time_now'
				ORDER BY m.match_date";
		$result =$this->db1->query($sql);
		$matchids_array = $result->result_array();
		$matchids = array();
		foreach ($matchids_array as $matchid)
		{
			array_push($matchids, $matchid['matchid']);
		}
		return $matchids;
	}

	public function get_finished_matchids($playerid, $seasonids, $esportid, $limit = 50, $pointer = 0)
	{
		$sql = "SELECT m.matchid
				FROM matches m, players p, player_teams pt, league_teams lt, leagues l
				WHERE p.playerid = '$playerid'
					AND p.playerid = pt.playerid
					AND pt.teamid = lt.teamid
					AND l.leagueid = lt.leagueid
					AND l.esportid = '$esportid'
					AND lt.leagueid = m.leagueid
					AND (m.teamaid = pt.teamid OR m.teambid = pt.teamid)
					AND m.status = 'finished'
					AND (m.seasonid IN ('" . implode("','", $seasonids) . "'))
				ORDER BY m.match_date";
		$result =$this->db1->query($sql);
		$matchids_array = $result->result_array();
		$matchids = array();
		foreach ($matchids_array as $matchid)
		{
			array_push($matchids, $matchid['matchid']);
		}
		return $matchids;
	}

	public function get_finished_matchids_byteam($teamid, $seasonid, $esportid)
	{
		$sql = "SELECT m.matchid
				FROM matches m
				WHERE (m.teamaid = '$teamid' OR m.teambid = '$teamid')
					AND m.status = 'finished'
					AND m.seasonid = '$seasonid'
				ORDER BY m.match_date";
		$result =$this->db1->query($sql);
		return $result->result_array();
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
						l.league_typeid,
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
				$temp_match['league_typeid'] = $match['league_typeid'];
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
	
	private function _get_min_match_length($esportid)
	{
		switch ($esportid)
		{
			case '1':
				return self::LOL_MIN_MATCH_DURATION;
				break;
			
			default:
				# code...
				break;
		}
	}

	private function _get_max_match_length($esportid)
	{
		switch ($esportid)
		{
			case '1':
				return self::LOL_MAX_MATCH_DURATION;
				break;
			
			default:
				# code...
				break;
		}
	}

	private function _get_lol_player_stats($matchid)
	{
		$sql = "SELECT 	ls.*, 
						lc.name AS champion_name, 
						lc.sprite AS champion_icon, 
						lsp.sprite AS spell_icon, 
						lsp.spell_name
				FROM 	lol_statistics ls, 
						lol_champions lc, 
						lol_spells lsp
					WHERE ls.matchid = '$matchid'
						AND	ls.championId = lc.championid
						AND	(ls.spell1 = lsp.spellid OR ls.spell2 = lsp.spellid) ";
		$results = $this->db1->query($sql);
		$result = $results->result_array();
		$players = array();
		foreach ($result as $player_stats)
		{
			$playerid = $player_stats['playerid'];
			if(!array_key_exists($playerid, $players))
			{
				$players[$playerid]['in_loop'] = 'yes';

				$temp_stats = array();
				$temp_stats['teamId'] = $player_stats['teamId'];
				$temp_stats['summmonerId'] = $playerid;
				$temp_stats['championId'] = $player_stats['championId'];
				$temp_stats['champion_name'] = $player_stats['champion_name'];
				$temp_stats['champion_icon'] = $this->lol_image_formatter->to_image_url($player_stats['champion_icon'],'champion');
				$temp_stats['spell1id'] = $player_stats['spell1'];
				$temp_stats['spell1_name'] = $player_stats['spell_name'];
				$temp_stats['spell1_icon'] = $this->lol_image_formatter->to_image_url($player_stats['spell_icon'],'spell');
				$temp_stats['spell2id'] = $player_stats['spell2'];

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
				for ($i=0; $i < 7; $i++)
				{ 
					$item_key = 'item'.$i;
					if(!array_key_exists($item_key, $stats))
					{
						$stats[$item_key] = 0;
					}
					else
					{
						$sprite = $stats[$item_key] . ".png";
						$img_url = $this->lol_image_formatter->to_image_url($sprite,'item');
						$stats[$item_key."_icon"] = $img_url;
					}
				}
				$temp_stats['stats'] = $stats;
				$players[$playerid] = $temp_stats;
			}
			else
			{
				$players[$playerid]['spell2_icon'] = $this->lol_image_formatter->to_image_url($player_stats['spell_icon'],'spell');
				$players[$playerid]['spell2_name'] = $player_stats['spell_name'];
			}
		}
		return $players;
	}

	public function get_matches_by_team($team, $finished = TRUE)
	{
		$match_status = $finished ? self::MATCH_FINISHED : self::MATCH_SCHEDULED;
		$seasonid = $team['leagues']['current_season'];
		$teamid = $team['teamid'];

		$sql = "SELECT m.* FROM matches m
				WHERE (teamaid = '$teamid' OR teambid = '$teamid')
				AND m.seasonid = '$seasonid'
				AND m.status = '$match_status'";
		$results = $this->db1->query($sql);
		$results = $results->result_array();
		return $results;
	}

	public function get_matches_by_leagueid($leagueid, $season) {
		$seasonid = $season['seasonid'];

		$sql = "SELECT * FROM matches
				WHERE leagueid = '$leagueid'
				AND seasonid = '$seasonid'
				ORDER BY match_date";
		$results = $this->db1->query($sql);
		$results = $results->result_array();
		return $results;
	}
}