<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Market_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}


	public function sign_up($playerid, $esportid){

		$sql = "INSERT INTO market (playerid, esportid)
          VALUES ('" . $playerid . "', '" . $esportid . "')";

	}


}
