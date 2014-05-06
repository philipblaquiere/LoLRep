<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_Model extends MY_Model {
  /**
  *Table columns:
  *UserId int
  *password varchar 64
  *email varchar 64
  *fname varchar 32
  *lname varchar 32
  *registertime timestamp
  *last_login_time timestamp
  *isvalidated tinyint default : 0
  */

  protected $table = 'users';
  protected $pkey = 'userid';

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function generate_rune_page_key()
  {
    $max_key_length = 8;
    $domain = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz123456789';
    $index_limit = strlen($domain) - 1;

    $key = '';
    for ($i = 0; $i < $max_key_length; $i++) {
      $key .= $domain[rand(0, $index_limit)];
    }
    return $key;
  }
  public function _generate_user_validation_key()
  { 
    $max_key_length = 16;
    $domain = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $index_limit = strlen($domain) - 1;

    $key = '';
    for ($i = 0; $i < $max_key_length; $i++) {
      $key .= $domain[rand(0, $index_limit)];
    }
    return $key;
  }

  public function get_by_uid($uid)
  {
    $sql = "SELECT * FROM {$this->table} WHERE UserId = '$uid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }
  public function get_by_email($email) {
    $sql = "SELECT * FROM {$this->table} WHERE email = '$email' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }
  public function get_uid_by_email($email) {
    $sql = "SELECT UserId FROM {$this->table} WHERE email = '$email' LIMIT 1";
    $query = $this->db1->query($sql);
    return $query->row_array();
  }

  /*
  * Returns key to be appended to url in email for user account validation
  */
  public function create($user)
  {
    $uniqueid = $this->generate_unique_key();
    $sql = "INSERT INTO users (userid, password, email, first_name, last_name, time_zone)
            VALUES ('" . $uniqueid . "', '" . $user['password'] . "', '" . $user['email'] . "', '" . $user['fname'] . "', '" . $user['lname'] . "', '" . $user['timezone'] . "')";
    
    $query = $this->db1->query($sql);

    $newuser = array();
    $newuser['userid'] = $uniqueid;
    $newuser['email'] = $user['email'];
    $newuser['key'] =  $this->_pend_valdiation($newuser);
    
    return $newuser;
  }

  public function validate_user($key, $uid) 
  {
    $sql = "SELECT * FROM user_pending WHERE uid = '$uid' and validation_key = '$key' LIMIT 1";
    $query = $this->db1->query($sql);
    if($query==true)
    {
      //user is validated, update user in table 'users'
      $sql ="UPDATE users SET validated = 1 WHERE userid = '$uid'";
      $this->db1->query($sql);
      return true;
    }
    else
    {
      return false;
    }
  }

  /*
  * Creats a validation key for user and adds it to pending accounts.
  */
  public function _pend_valdiation($newuser)
  {
    $key = $this->_generate_user_validation_key();
    $sql = "INSERT INTO user_pending (userid, email, validation_key) 
            VALUES ('". $newuser['userid'] ."', '". $newuser['email'] ."', '". $key ."')";
    $this->db1->query($sql);
    return $key;
  }

  public function log_login($uid)
  {
    $sql = "UPDATE {$this->table} 
            SET last_login_time = current_timestamp 
            WHERE userid = '$uid'";
    $this->db1->query($sql);
  }
}
