<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Invite_model extends MY_Model {

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function invite($invitation, $esportid) 
  {
    $sql = "INSERT INTO invites (inviteid, teamid, esportid, playerid, message, status) 
            VALUES ('". $this->generate_unique_key() ."','". $invitation['teamid'] ."','". $esportid ."', '". $invitation['playerid'] ."', '". $invitation['message'] ."','new')";
    $this->db1->query($sql);
    return;
  }

  public function get_invites_by_uid($uid, $esportid) 
  {
    $sql = "SELECT  i.invitedid AS inviteid,
                    i.teamid AS teamid,
                    t.team_name AS team_name,
                    i.esportid AS esportid,
                    i.playerid AS playerid,
                    i.message AS message,
                    i.invite_date AS invite_date,
                    i.status AS status
                    FROM invites i, teams t, players p, user_players up
                    WHERE up.userid = '$uid' 
                      AND i.esportid = '$esportid'
                      AND up.playerid = p.playerid
                      AND i.teamid = t.teamid
                      AND p.playerid = i.playerid";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function get_invite_byid($inviteid)
  {
    $sql = "SELECT * FROM invite_lol 
            WHERE inviteid = '$inviteid' 
            LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }
  
  public function mark_invites_read($uid)
  {
    $sql = "UPDATE invite_lol i
            INNER JOIN user_players up ON up.userid = '$uid'
            INNER JOIN players p ON p.playerid = up.playerid
            SET i.status='read' 
            WHERE i.playerid = p.playerid AND i.status = 'new'";;
    $result = $this->db1->query($sql);
  }

  public function mark_invite_accepted($inviteid,$esportid)
  {
          $sql = "UPDATE team_invite_lol
                  SET status='accepted'
                  WHERE inviteid = '$inviteid'
                    AND esportid = '$esportid'";
          $this->db1->query($sql);
    }
  }

  public function mark_invite_declined($inviteid,$esportid)
  {
          $sql = "UPDATE team_invite_lol
                  SET status='declined'
                  WHERE inviteid = '$inviteid'
                    AND esportid = '$esportid'";
          $this->db1->query($sql);
    }
  }
}
