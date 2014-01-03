<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Team_invite_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function invite_summoner($invitation) {
    $sql = "INSERT INTO team_invite_lol (teamid, summonerid, message) 
            VALUES ('". $invitation['teamid'] ."', '". $invitation['summonerid']['SummonerId'] ."', '". $invitation['message'] ."')";
    $this->db1->query($sql);
  }
  public function get_lol_invites_by_uid($uid) {
    /*teamid
    summonerid
    message
    invite_date
    status
    teamid
    name
    esportid
    created
    captainid
    countryid
    stateid
    regionid
    UserId
    SummonerId
    SummonerName
    ProfileIconId
    RevisionDate
    SummonerLevel
    created*/
    $sql = "SELECT * FROM team_invite_lol i 
            INNER JOIN teams t ON t.teamid = i.teamid
            INNER JOIN summoners s ON s.UserId = '$uid'
            WHERE s.SummonerId = i.summonerid";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  
  public function mark_invites_read($uid) {
    $sql = "UPDATE team_invite_lol t
            INNER JOIN summoners s ON s.UserId = '$uid'
            SET t.status='read' 
            WHERE t.summonerid = s.SummonerId AND t.status = 'new'";;
    $result = $this->db1->query($sql);
  }
}
