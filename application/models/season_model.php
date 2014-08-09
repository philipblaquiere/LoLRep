<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Season_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

	public function start_season($seasonid, $start_date, $end_date, $teams)
  {
		$sql = "UPDATE seasons
			SET season_status = 'active', start_date = '$start_date', end_date = '$end_date'
			WHERE seasonid = '$seasonid'";

    $sql2 = "INSERT INTO season_teams(seasonid, teamid) VALUES ";
    foreach ($teams as $teamid)
    {
      $sql2 .= "('" . $seasonid ."','".$teamid ."'),";
    }
    $sql2 = substr($sql, 0, -1);

    $this->db1->trans_start();
		$this->db1->query($sql);
    $this->db1->query($sql2);
    $this->db1->trans_complete();
	}
}