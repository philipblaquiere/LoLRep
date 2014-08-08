<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message_model extends MY_Model {

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function get_inbox($userid, $esportid)
  {
    $sql = "SELECT m.messageid,
                   m.sequence,
                   m.created_on,
                   m.created_by,
                   m.body,
                   r.status
            FROM messages_recipients AS r
            INNER JOIN messages AS m on m.messageid = r.messageid
            WHERE r.userid= '$userid'
              AND r.status IN ('Active', 'New')
              AND r.sequence = (SELECT max(rr.sequence)
                                FROM messages_recipients rr
                                WHERE rr.messageid = m.messageid
                                AND rr.status IN ('Active', 'New'))
              AND if (m.sequence = 1 AND m.created_by = '$userid', 1=0, 1=1)
              ORDER BY created_on DESC";
  }

  public function get_sequence($messageid)
  {

  }

  public function add_message($message)
  {
    $sequenceid = $message['sequenceid'];//....
    $msg_body = $message['msg_body'];

    $sql = "INSERT INTO messages (messageid, sequence, created_on_ip, created_by, body)
            VALUES ('$messageid', '$sequence', '$created_on_ip', '$created_by', '$body')";

  }
}

