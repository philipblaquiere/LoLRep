<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Banned_model extends CI_Model {

	public function __construct() {
	    parent::__construct();
	    $this->db1 = $this->load->database('default', TRUE);
 	}

 	public function get_summoner_byemail($email)
 	{
 		$sql = "SELECT * FROM users u
 				INNER JOIN summoners s
 				ON u.UserId = s.UserId
 			 	WHERE u.email = '$email'";
 		$result = $this->db1->query($sql);
	    return $result->row_array();
 	}

 	public function get_summoner_by_summonername($summonername)
 	{
 		$sql = "SELECT * FROM users u
 				INNER JOIN summoners s
 				ON u.UserId = s.UserId
 			 	WHERE s.SummonerName = '$summonername'";
 		$result = $this->db1->query($sql);
	    return $result->row_array();
	    
 	}
 	public function ban_summoner($user, $reason) {
 		$sql = "INSERT INTO banned_summoners (Email, SummonerName, SummonerId, reason)
	    		VALUES ('" . $user['email'] . "', '" . $user['SummonerName'] . "', '" . $user['SummonerId'] . "', '" . $reason . "')";
	    $this->db1->query($sql);
 	}

 	public function get_byemail($email) {
 		$sql = "SELECT * FROM banned_summoners
 			 	WHERE email = '$email'
 			 	LIMIT 1";
	    $result = $this->db1->query($sql);
	    return $result->row_array();
 	}
 	public function get_bysummonername($summonername) {
 		$sql = "SELECT * FROM banned_summoners
 			 	WHERE SummonerName = '$summonername'
 			 	LIMIT 1";
	    $result = $this->db1->query($sql);
	    return $result->row_array();
 	}
 }
