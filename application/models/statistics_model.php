<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics_model extends MY_Model
{

	private $lol_keys;

	public function __construct()
	{
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

	public function get_team_stats($teamid, $leagueid, $seasonid, $esportid)
	{
		switch ($esportid) {
			case '1':
				return $this->_get_lol_team_stats($teamid, $leagueid, $seasonid);
				break;
			
			default:
				# code...
				break;
		}
	}
	private function _get_lol_team_stats($teamid, $leagueid, $seasonid)
	{
		$sql = "SELECT 	ls.goldEarned, 
						ls.numDeaths, 
						ls.assists, 
						ls.championsKilled,
						ls.timePlayed,
						ls.minionsKilled,
						pt.playerid,
						m.matchid
				FROM lol_statistics ls, matches m, player_teams pt
				WHERE (m.teamaid = '$teamid' OR m.teambid = '$teamid')
					AND pt.teamid = '$teamid'
					AND m.leagueid = '$leagueid'
					AND m.seasonid = '$seasonid'
					AND m.matchid = ls.matchid
					AND pt.playerid = ls.playerid
					AND m.status = 'finished'";

		$result = $this->db1->query($sql);
		$result = $result->result_array();

		$team_stats = array();
		$player_stats = array();
		$team_stats['teamid'] = $teamid;
		$team_stats['leagueid'] = $leagueid;
		$team_stats['seasonid'] = $seasonid;

		foreach ($result as $stats)
		{
			$game = array();
			$game['numDeaths'] = $stats['numDeaths'];
			$game['assists'] = $stats['assists'];
			$game['championsKilled'] = $stats['championsKilled'];
			$game['timePlayed'] = $stats['timePlayed'];
			$game['goldEarned'] = $stats['goldEarned'];
			$game['minionsKilled'] = $stats['minionsKilled'];
			if(!array_key_exists($stats['playerid'], $player_stats))
			{
				$player_stats[$stats['playerid']] = array();
			}
			$player_stats[$stats['playerid']][$stats['matchid']] = $game;
		}

		$team_stats['player_stats'] = $player_stats;

		return $team_stats;
	}

	public function add_match_stats($matches, $esportid)
	{
		switch ($esportid) {
			case '1':
				return $this->_add_lol_stats($matches);

				break;
			
			default:
				# code...
				break;
		}
	}

	private function _add_lol_stats($matches)
	{
		$teams_all = array();
		$keys = $this->_lol_keys();

		$sql = "INSERT INTO lol_statistics (";

		// loop over the array
	    foreach ($keys as $key)
	    {
	        // add to the query
	        $sql .= $key . ",";
	    }
	    $sql = substr($sql, 0, -1);
	    $sql .= ") VALUES ";
	

		foreach ($matches as $match)
		{
			$matchid = $match['matchid'];
			$teama = $match['teama']['teama_players'];
			$teamb = $match['teamb']['teamb_players'];
			$teams_all = $teama + $teamb;
		
			foreach ($teams_all as $player)
			{
				$player['matchid'] = $matchid;
				$player['playerid'] = $player['summonerId'];
				unset($player['summonerId']);
				if(array_key_exists('stats', $player))
				{
					$player = $player + $player['stats'];
					unset($player['stats']);
				}

	 			$sql .= "(";
			    foreach ($keys as $key)
			    {
			    	if(array_key_exists($key, $player))
			    	{
			    		$sql .= "'".$player[$key]."',";
			    	}
			    	else
			    	{
			    		$sql .= "'',";
			    	}
			    }
			    $sql = substr($sql, 0, -1);
			    $sql .= "),";
			}
		}
		$sql = substr($sql, 0, -1);
		$sql .= ";";
		$this->db1->query($sql);
		return;
	}

	private function _lol_keys()
	{
		if(empty($this->lol_keys))
		{
			$this->lol_keys = array(
						'matchid',
						'playerid',
						'spell1',
						'teamId',
						'level',
						'goldEarned',
						'numDeaths',
						'turretsKilled',
						'minionsKilled',
						'championsKilled',
						'totalDamageDealt',
						'totalDamageTaken',
						'doubleKills',
						'tripleKills',
						'quadraKills',
						'pentaKills',
						'win',
						'item0',
						'item1',
						'item2',
						'item3',
						'item4',
						'item5',
						'assists',
						'spell2',
						'championId',
						'barracksKilled',
						'combatPlayerScore',
						'consumablesPurchased',
						'damageDealtPlayer',
						'firstBlood',
						'gold',
						'goldSpent',
						'item6',
						'itemsPurchased',
						'killingSprees',
						'largestCriticalStrike',
						'largestKillingSpree',
						'largestMultiKill',
						'legendaryItemsCreated',
						'magicDamageDealtPlayer',
						'magicDamageDealtToChampions',
						'magicDamageTaken',
						'neutralMinionsKilled',
						'neutralMinionsKilledEnemyJungle',
						'neutralMinionsKilledYourJungle',
						'nexusKilled',
						'physicalDamageDealtPlayer',
						'physicalDamageDealtToChampions',
						'physicalDamageTaken',
						'sightWardsBought',
						'spell1Cast',
						'spell2Cast',
						'spell3Cast',
						'spell4Cast',
						'summonSpell1Cast',
						'summonSpell2Cast',
						'superMonsterKilled',
						'timePlayed',
						'totalHeal',
						'totalTimeCrowdControlDealt',
						'totalUnitsHealed',
						'visionWardsBought',
						'wardKilled',
						'wardPlaced'

				);
		}
		return $this->lol_keys;
	}
}