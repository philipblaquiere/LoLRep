<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

	public function create($userid, $player, $esportid)
	{
	    $tier = $player['rank'][$player['playerid']]['tier'];
	    if($tier)
	    {
	    	//iterate through League to find Summoner Rankings
		    foreach ($player['rank'][$$player['playerid']]['entries'] as $entry)
		    {
		     	if($entry['playerOrTeamId'] == $player['playerid'])
		     		$rank = $entry['rank'];
		    }
	    } 
	   else 
	    {
	    	$rank = "unranked";
	    	$tier = "unranked";
	    }
	    $sql = "INSERT INTO players (playerid, player_name, esportid, region, icon, level, rank, tier) 
	            VALUES ('" . $player['playerid'] . "','" . $player['player_name'] . "', '" . $esportid . "',  '" . $player['region'] . "','" . $player['icon'] . "', '" . $player['level'] . "', '" . $rank . "', '" . $tier . "')";
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
						p.icon as icon,
						p.level as level,
						p.rank as rank,
						p.tier as tier
				FROM players p
				WHERE player_name = '$player_name'
					AND esportid = '$esportid'
				LIMIT 1";
		$result = $this->db1->query($sql);
		return $result->row_array();
	}

	public function get_player_by_userid($uid, $esportid) {
		$sql = "SELECT 	p.player_name as player_name,
						p.playerid as playerid,
						p.region as region,
						p.icon as icon,
						p.level as level,
						p.rank as rank,
						p.tier as tier
						FROM players p, user_players up
						WHERE up.userid = '$uid'
							AND p.playerid = up.playerid 
							AND p.esportid = '$esportid'
						LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_player_by_email($email, $esportid) {
		$sql = "SELECT 	p.player_name as player_name,
						p.playerid as playerid,
						p.region as region,
						p.icon as icon,
						p.level as level,
						p.rank as rank,
						p.tier as tier
						FROM players AS p, users AS u, user_players AS up  
						WHERE u.email = '$email' 
						AND u.userid = up.userid 
						AND p.playerid = up.playerid 
						AND p.esportid = '$esportid'
						LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}
}
