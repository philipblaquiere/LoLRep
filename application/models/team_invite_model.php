<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Team_invite_model extends MY_Model {

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function invite_summoner($invitation) {
    $sql = "INSERT INTO team_invite_lol (inviteid, teamid, summonerid, message,status) 
            VALUES ('". $this->generate_unique_key() ."','". $invitation['teamid'] ."', '". $invitation['summonerid']['SummonerId'] ."', '". $invitation['message'] ."','new')";
    $this->db1->query($sql);
  }
  public function get_lol_new_invites_by_uid($uid) {
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
            WHERE s.SummonerId = i.summonerid AND i.status = 'new' OR i.status = 'read'";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function get_invite_byid($inviteid) {
    $sql = "SELECT * FROM team_invite_lol 
            WHERE inviteid = '$inviteid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }
  
  public function mark_invites_read($uid) {
    $sql = "UPDATE team_invite_lol t
            INNER JOIN summoners s ON s.UserId = '$uid'
            SET t.status='read' 
            WHERE t.summonerid = s.SummonerId AND t.status = 'new'";;
    $result = $this->db1->query($sql);
  }

  public function mark_invite_accepted($inviteid,$esportid) {
    switch ($esportid) {
      case '1':
          $sql = "UPDATE team_invite_lol
              SET status='accepted'
              WHERE inviteid = '$inviteid'";
              ;
          $this->db1->query($sql);
        break;
      
      default:
        # code...
        break;
    }
  }
  public function mark_invite_declined($inviteid,$esportid) {
    switch ($esportid) {
      case '1':
          $sql = "UPDATE team_invite_lol
              SET status='declined'
              WHERE inviteid = '$inviteid'";
              ;
          $this->db1->query($sql);
        break;
      
      default:
        # code...
        break;
    }
  }
}
