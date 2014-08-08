<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

	public function create($userid, $player, $esportid)
	{
	    $sql = "INSERT INTO players (playerid, player_name, esportid, region, icon) 
	            VALUES ('" . $player['playerid'] . "','" . $player['player_name'] . "', '" . $esportid . "',  '" . $player['region'] . "','" . $player['icon'] . "')";
		$result = $this->db1->query($sql);

		//relate the playerid to the userid in the user_players table
	    $sql = "INSERT INTO user_players (userid, playerid) 
	            VALUES ('" . $userid . "','" . $player['playerid'] . "')";
	    $result = $this->db1->query($sql);
		return;
	}

	public function get_player($playerid, $esportid)
	{
		$sql = "SELECT p.*, lt.teamid, t.team_name, l.leagueid, l.league_name, s.seasonid, s.season_status, s.start_date,s.end_date
				FROM players p,  player_teams pt, teams t, leagues l, league_teams lt, seasons s, season_teams st, season_leagues sl
				WHERE p.playerid = '$playerid'
					AND p.esportid = '$esportid'
					AND p.playerid = pt.playerid
					AND pt.teamid = st.teamid
                    AND pt.teamid = lt.teamid 
                    AND st.teamid = lt.teamid
                    AND st.seasonid = sl.seasonid
                    AND sl.leagueid = lt.leagueid
                    AND l.leagueid = lt.leagueid
                    AND s.seasonid = st.seasonid
                    AND t.teamid = pt.teamid";
		$result = $this->db1->query($sql);
		$results = $result->result_array();

		$player = array();

		if(!empty($results))
		{
			foreach ($results as $player_result)
			{
				if(empty($player))
				{
					$player['playerid'] = $player_result['playerid'];
					$player['player_name'] = $player_result['player_name'];
					$player['region'] = $player_result['region'];
					$player['icon'] = $player_result['icon'];
					$player['teams'] = array();
					$player['teams_meta'] = array();
				}

				if(array_key_exists('teamid', $player_result))
				{
					if(!array_key_exists($player_result['teamid'], $player['teams_meta']))
					{
						array_push($player['teams'], $player_result['teamid']);
					}
				}
				if(array_key_exists('leagueid', $player_result))
				{
					if(!array_key_exists($player_result['teamid'], $player['teams_meta']))
					{
						$player['teams_meta'][$player_result['teamid']]['team'] = $player_result['teamid'];
						$player['teams_meta'][$player_result['teamid']]['team_name'] = $player_result['team_name'];
					}
					$league['leagueid'] = $player_result['leagueid'];
					$league['league_name'] = $player_result['league_name'];
					$league['seasons'] = array();
					if(array_key_exists('seasonid', $player_result))
					{
						if($player_result['season_status'] == 'active')
						{
							$player['teams_meta'][$player_result['teamid']]['current_league'] = $player_result['leagueid'];
							$player['teams_meta'][$player_result['teamid']]['current_season'] = $player_result['seasonid'];
						}
						$league['seasons'][$player_result['seasonid']]['seasonid'] = $player_result['seasonid'];
						$league['seasons'][$player_result['seasonid']]['start_date'] = $player_result['start_date'];
						$league['seasons'][$player_result['seasonid']]['end_date'] = $player_result['end_date'];
					}
					if(array_key_exists('leagues', $player['teams_meta'][$player_result['teamid']]) && array_key_exists($player_result['leagueid'], $player['teams_meta'][$player_result['teamid']]['leagues']))
					{
						$player['teams_meta'][$player_result['teamid']]['leagues'][$player_result['leagueid']]['seasons'][$player_result['seasonid']] = $league['seasons'][$player_result['seasonid']];
					}
					else
					{
						$player['teams_meta'][$player_result['teamid']]['leagues'][$player_result['leagueid']] = $league;
					}
					
				}
			}
			$player['registered'] = TRUE;
		}
		return $player;
	}

	public function get_player_by_name($player_name, $esportid)
	{
		$sql = "SELECT 	p.player_name as player_name,
						p.playerid as playerid,
						p.region as region,
						p.icon as icon
				FROM players p
				WHERE player_name = '$player_name'
					AND esportid = '$esportid'
				LIMIT 1";
		$this->db1->trans_start();
		$result = $this->db1->query($sql);
	    $player = $result->row_array();
	    if(array_key_exists('playerid', $player))
	    {
	    	$playerid = $player['playerid'];

			$sql = "SELECT 	pt.teamid AS teamid
							FROM player_teams AS pt, teams AS t, league_teams as lt
							WHERE pt.playerid = '$playerid'
								AND t.teamid = pt.teamid
								AND lt.teamid = t.teamid
								AND lt.status = 'active'";
			$result = $this->db1->query($sql);
			
			$result = $result->result_array();
			$player['teams'] = array();
			if($result)
			{
				$player['teams'] = $result[0];
			}
	    }
	    $this->db1->trans_complete();
	    return $player;
	}

	public function get_player_by_userid($uid, $esportid) 
	{
		$sql = "SELECT 	p.player_name as player_name,
						p.playerid as playerid,
						p.region as region,
						p.icon as icon
						FROM players p, user_players up
						WHERE up.userid = '$uid'
							AND p.playerid = up.playerid 
							AND p.esportid = '$esportid'
						LIMIT 1";
		$this->db1->trans_start();
		$result = $this->db1->query($sql);
	    $player = $result->row_array();
	    $playerid = $player['playerid'];

		$sql = "SELECT 	pt.teamid AS teamid
						FROM player_teams AS pt, teams AS t, league_teams as lt
						WHERE pt.playerid = '$playerid'
							AND t.teamid = pt.teamid
							AND lt.teamid = t.teamid
							AND lt.status = 'active'";
		$result = $this->db1->query($sql);
		$this->db1->trans_complete();
		$result = $result->result_array();
		$player['teams'] = array();
		if($result)
		{
			$player['teams'] = $result[0];
		}
	    return $player;
	}

	public function get_player_by_email($email, $esportid) 
	{
		$sql = "SELECT 	p.player_name as player_name,
						p.playerid as playerid,
						p.region as region,
						p.icon as icon
						FROM players AS p, users AS u, user_players AS up
						WHERE u.email = '$email'
							AND u.userid = up.userid 
							AND p.playerid = up.playerid 
							AND p.esportid = '$esportid'
						LIMIT 1";
		$this->db1->trans_start();
		$result = $this->db1->query($sql);
	    $player = $result->row_array();
	    if(!empty($player))
	    {
	    	$playerid = $player['playerid'];
			$sql = "SELECT 	pt.teamid AS teamid
							FROM player_teams AS pt, teams AS t, league_teams as lt
							WHERE pt.playerid = '$playerid'
								AND t.teamid = pt.teamid
								AND lt.teamid = t.teamid
								AND lt.status = 'active'";
			$result = $this->db1->query($sql);
			$result = $result->result_array();
			$player['teams'] = array();
			if($result)
			{
				$player['teams'] = $result[0];
			}
	    }
	    $this->db1->trans_complete();
	    return $player;
	}
}
