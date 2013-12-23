<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_Model extends CI_Model {
  /**
  *Table columns:
  *UserId int
  *password varchar 64
  *salt varchar 64
  *email varchar 64
  *fname varchar 32
  *lname varchar 32
  *cityid
  *provincestateid int
  *countryid int
  *registertime timestamp
  *last_login_time timestamp
  *isvalidated tinyint default : 0
  */

  protected $table = 'users';
  protected $pkey = 'UserId';

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  /**
   * Hash a password using the user's unique salt.
   */
  public function _password_hash($password, $salt) {
    // append the salt to thwart rainbow tables
    return sha1($password . $salt);
  }

  /**
   * Hash a password using the user's unique salt.
   */
  public function _generate_salt() {
    $max_salt_length = 6;
    $domain = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-=!@#$%^&*()_+`~';
    $index_limit = strlen($domain) - 1;

    $salt = '';
    for ($i = 0; $i < $max_salt_length; $i++) {
      $salt .= $domain[rand(0, $index_limit)];
    }
    return $salt;
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

  public function validate_password($user,$password) {
    if(!$password || !$user['email'])
      return false;
    return $user['password'] === $this->_password_hash($password, $user['salt']);
  }

  /*
  * Returns key to be appended to url in email for user account validation
  */
  public function create($user){
    $password = $user->password;
    $salt = $user->salt;
    $email = $user->email;
    $fname = $user->fname;
    $lname = $user->lname;
    $regionid = $user->regionid;
    $provincestateid = $user->provincestateid;
    $countryid = $user->countryid;
    $sql = "INSERT INTO users (password, salt, email, firstname, lastname, regionid, provincestateid, countryid) 
            VALUES ('" . $password . "', '" . $salt . "', '" . $email . "', '" . $fname . "', '" . $lname . "', '" . $regionid . "', '" . $provincestateid . "', '" . $countryid . "')";
    $query = $this->db1->query($sql);
    $newuser = array();
    $uid = $this->get_uid_by_email($email);
    $newuser['uid'] = $uid['UserId'];
    $newuser['email'] = $email;
    $newuser['key'] =  $this->_pend_valdiation($newuser);
    
    return $newuser;
  }

  public function validate_user($key, $uid) {
    $sql = "SELECT * FROM pendingaccounts WHERE uid = '$uid' and validationkey = '$key' LIMIT 1";
    $query = $this->db1->query($sql);
    if($query==true)
    {
      //user is validated, update user in table 'users'
      $sql ="UPDATE users SET validated = 1 WHERE UserId = '$uid'";
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
  public function _pend_valdiation($newuser) {
    $key = $this->_generate_user_validation_key();
    $sql = "INSERT INTO pendingaccounts (uid, email, validationkey) 
            VALUES ('". $newuser['uid'] ."', '". $newuser['email'] ."', '". $key ."')";
    $this->db1->query($sql);
    return $key;
  }

  public function log_login($uid){
    $sql = "UPDATE {$this->table} SET last_login_time = current_timestamp WHERE UserId = '$uid'";
    $result = $this->db1->query($sql);
  }



}
