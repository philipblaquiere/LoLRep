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
			$this->db1->trans_complete();
			$result = $result->result_array();
			$player['teams'] = array();
			if($result)
			{
				$player['teams'] = $result[0];
			}
	    }
	    
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
}
