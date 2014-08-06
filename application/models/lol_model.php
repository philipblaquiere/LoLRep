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

	public function update_lol_spells($spells)
	{
		$sql = "DELETE FROM lol_spells";
        //clear db table
        $this->db1->query($sql);

        $sql = "INSERT INTO lol_spells (spellid, spell_name, sprite) VALUES ";
		foreach ($spells['data'] as $spells) {
			if(count($spells) > 2)
			{
				$sql .= "('" . $spells['id'] . "','" . rawurlencode($spells['name']) . "','" . $spells['image']['full'] . "'),";
			}
        }
        $this->db1->query(trim($sql, ","));
	}

	public function update_lol_champions($champion)
	{
        $sql = "DELETE FROM lol_champions";
        //clear db table
        $this->db1->query($sql);

        $sql = "INSERT INTO lol_champions (championid, name, sprite) VALUES ";
		foreach ($champion['data'] as $champion) {
			if(count($champion) > 2)
			{
				$sql .= "('" . $champion['id'] . "','" . rawurlencode($champion['name']) . "','" . $champion['image']['full'] . "'),";
			}
        }
        $this->db1->query(trim($sql, ","));
	}

	public function update_lol_items($items)
	{
		$sql = "INSERT INTO lol_items (itemid, name, sprite) VALUES ";
		foreach ($items['data'] as $item) {
			if(count($item) > 2)
			{
				$sql .= "('" . $item['id'] . "','" . rawurlencode($item['name']) . "','" . $item['image']['full'] . "'),";
			}
        }

        $sql = trim($sql, ",");
        $sql .= " ON DUPLICATE KEY UPDATE name=VALUES(name), sprite=VALUES(sprite);";
        $this->db1->query($sql);
	}
	
}