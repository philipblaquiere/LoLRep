<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lol_model extends CI_Model {

	public function __construct() {
	    parent::__construct();
	    $this->db1 = $this->load->database('default', TRUE);
 	}


  	public function registered_summoner($summonername) {
	    $sql = "SELECT SummonerName FROM summoners WHERE SummonerName = '$summonername' LIMIT 1";
	    $result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_summonername_from_uid($uid) {
		$sql = "SELECT SummonerName FROM summoners WHERE UserId = '$uid' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_summonerid_from_summonername($summonername) {
		$sql = "SELECT SummonerId FROM summoners WHERE SummonerName = '$summonername' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_summonerid_from_uid($uid) {
		$sql = "SELECT SummonerId FROM summoners WHERE UserId = '$uid' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_uid_from_summonerid($summonerid) {
		$sql = "SELECT UserId FROM summoners WHERE SummonerId = '$summonerid' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function update_summoner_ranking($summoner) {
		
	}

	public function create_summoner($uid, $summoner) {

		$SummonerId = $summoner['id'];
	    $SummonerName = $summoner['name'];
	    $ProfileIconId = $summoner['profileIconId'];
	    $RevisionDate = $summoner['revisionDate'];
	    $SummonerLevel = $summoner['summonerLevel'];
	    $summonertier = $summoner['summonerrank'][$SummonerId]['tier'];
	    if($summonertier) {
		    foreach ($summoner['summonerrank'][$SummonerId]['entries'] as $entry) {
		     	if($entry['playerOrTeamId'] == $SummonerId)
		     		$summonerrank = $entry['rank'];
		    }
	    } 
	   else {
	    	$summonerrank = "unranked";
	    	$summonertier = "unranked";
	    }
	    $sql = "INSERT INTO summoners (UserId, SummonerId, SummonerName, ProfileIconId, RevisionDate, SummonerLevel, rank, tier) 
	            VALUES ('" . $uid . "','" . $SummonerId . "', '" . $SummonerName . "', '" . $ProfileIconId . "', '" . $RevisionDate . "', '" . $SummonerLevel . "', '" . $summonertier . "', '" . $summonerrank . "')";
		$result = $this->db1->query($sql);
		return;
	}

	public function update_lol_champions($champions) {
        $sql = "DELETE FROM champions_lol";
        //clear db table
        $this->db1->query($sql);

        $sql = "INSERT INTO champions_lol (championid,rankedPlayEnabled, name, active, freeToPlay) VALUES ";
        foreach ($champions['champions'] as $champion) {
            $sql .= "('" . $champion['id'] . "','" . $champion['rankedPlayEnabled'] . "','" . $champion['name'] . "','" . $champion['active'] . "','" . $champion['freeToPlay'] . "'),";
        }
        $this->db1->query(trim($sql, ","));

	}
	
}