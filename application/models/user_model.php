<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('crud_model.php');
class User_Model extends Crud_Model {
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

  /**
   * Authenticate user's login credentials
   */
  public function get_by_email($email) {
    $sql = "SELECT * FROM {$this->table} WHERE email = :email";
    $bindings = array(':email' => $email);
    $result = $this->database_layer->query($sql, $bindings);
    return $result->fetchObject();
  }

  public function validate_password($email, $password) {
    $user = $this->get_by_email($email);
    return $user->password === $this->_password_hash($password, $user->salt);
  }

  public function save($user){
    unset($user->register_time);
    return parent::save($user);
  }

  public function log_login($uid){
    $sql = "UPDATE {$this->table} SET last_login_time = current_timestamp WHERE {$this->pkey} = :uid";
    $bindings = array(':uid' => $uid);
    $result = $this->database_layer->query($sql, $bindings);
  }
}
