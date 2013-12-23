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
		$sql = "SELECT s.SummonerName as SummonerName FROM summoners s INNER JOIN usersummoners u ON  s.SummonerId = u.SummonerId WHERE u.UserId = '$uid' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_uid_from_summonername($summonername) {
		$sql = "SELECT u.UserId as UserId FROM usersummoners u INNER JOIN summoners s ON  u.SummonerId = s.SummonerId WHERE s.SummonerName = '$summonername' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_summonerid_from_uid($uid) {
		$sql = "SELECT SummonerId FROM usersummoners WHERE UserId = '$uid' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}

	public function get_uid_from_summonerid($summonerid) {
		$sql = "SELECT UserId FROM usersummoners WHERE SummonerId = '$summonerid' LIMIT 1";
		$result = $this->db1->query($sql);
	    return $result->row_array();
	}


	public function create_summoner($uid, $summoner) {
		$sql = "INSERT UserId FROM usersummoners WHERE SummonerId = '$summonerid' LIMIT 1";
		$SummonerId = $summoner->summonerId;
	    $SummonerName = $summoner->summonerName;
	    $ProfileIconId = $summoner->profileIconId;
	    $RevisionDate = $summoner->revisionDate;
	    $SummonerLevel = $summoner->summonerLevel;

		$sql = "INSERT INTO usersummoners (UserId, SummonerId) 
	            VALUES ('" . $uid . "', '" . $SummonerId . "')";
	    $result = $this->db1->query($sql);

	    $sql = "INSERT INTO summoners (SummonerId, SummonerName, ProfileIconId, RevisionDate, SummonerLevel) 
	            VALUES ('" . $SummonerId . "', '" . $SummonerName . "', '" . $ProfileIconId . "', '" . $RevisionDate . "', '" . $SummonerLevel . "')";
		$result = $this->db1->query($sql);
		return;
	}
}