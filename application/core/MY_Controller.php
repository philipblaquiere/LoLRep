<?php
/**
 * @file
 * Extends the base controller to load the PHP session upon construction. Also
 * provides a convienient way to render a view and check if the user is logged
 * in.
 */
class MY_Controller extends CI_Controller  {

  private $TIMEZONE_DEFAULT = "UTC";

  public function __construct()
  {
    parent::__construct();
    session_start();
    //$this->load->library('database_layer');
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('date');
    //load all models
    $this->set_esportid(1);
  }

  /**
   * Load the specified View, automatically wrapping it between the site's
   * header and footer.
   */
  public function view_wrapper($template, $data = array(), $display_messages = TRUE) {
    $data['system_messages'] = array();
    if ($display_messages) {
      $data['system_messages'] = $this->system_message_model->get_messages();
    }
    $data['is_logged_in'] = $this->is_logged_in();
    //$data['is_admin_user'] = $this->is_admin_user();
    $this->load->view('include/header', $data);

    if (!isset($_SESSION['user']) && $data['is_logged_in']){
      $this->set_current_user($data['is_logged_in']);
      $data['user'] = $_SESSION['user'];
    }

    $this->load->view('include/navigation', $data);
    $this->load->view('include/system_messages', $data);
    $this->load->view($template, $data);
    $this->load->view('include/footer');
  }

  /**
   * Convinience function to determine if the user is logged in.
   * @returns
   *   TRUE if the user is currently authenticated, FALSE otherwise.
   */
  protected function is_logged_in() {
    return isset($_SESSION['userid']);
  }

  protected function is_admin_user() {
    if ($uid = $this->get_current_user()) {
      $roles = $this->user_roles_model->get_roles_by_uid($uid);
      foreach ($roles as $role) {
        if ($role->rid == 4) { // administrator
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Convinience function to get the ID of the currently logged in user.
   */
  protected function get_userid() 
  {
    return isset($_SESSION['user']) ? $_SESSION['user']['userid'] : 0;
  }

  protected function set_user($user)
  {
    $_SESSION['userid'] = $user['userid'];
    $_SESSION['user'] = $user;
    $_SESSION['esportid'] = $this->get_esportid();
  }

  protected function set_player($player)
  {
    $_SESSION['player'] = $player;
    $_SESSION['player']['esportid'] = $this->get_esportid();
  }
  protected function get_player()
  {
    return isset($_SESSION['player']) ? $_SESSION['player'] : 0;
  }
  protected function player_exists()
  {
    return isset($_SESSION['player']);
  }

  protected function destroy_session() 
  {
    unset($_SESSION['userid']);
    unset($_SESSION['user']);
    unset($_SESSION['last_login']);
    unset($_SESSION['esportid']);
    unset($_SESSION['player']);
  }

  /**
   * Verifies the current user's session and redirects to the login form if the
   * user has not authenticated.
   */
  protected function require_login()
  {
    if (!$this->is_logged_in()) 
    {
      $this->system_message_model->set_message('Please login to access this page.', MESSAGE_WARNING);
      redirect('sign_in', 'refresh');
      die();
    }
  }

  /*
  * Verifies if the user is has registered his gaming account to the current set esport.
  */
  protected function require_registered()
  {
    return isset($_SESSION['player']) && $this->get_esportid() == $_SESSION['player']['esportid'];
  }

  /**
   * Verifies the current user's session and redirects to the login form if the
   * user has not authenticated.
   */
  protected function require_admin() 
  {
    if (!$this->is_logged_in() || !$this->is_admin_user())
    {
      $this->system_message_model->set_message('Administrator access is required to access this page.', MESSAGE_WARNING);
      show_error('Access denied: you must be logged in as an administrator to view this content.', 403);
    }
  }

  /**
  * Verifies that client is not logged in, and if so, it logs them out but allows them to view
  * the originally requested page (ie Registration)
  */
  protected function require_not_login()
  {
    if ($this->is_logged_in())
    {
      $this->system_message_model->set_message('You have been automatically logged out in order to view this page.', MESSAGE_WARNING);
      $this->destroy_session();
    }
  }
  protected function set_esportid($esportid) 
  {
    $_SESSION['esportid'] = $esportid;
  }

  protected function get_esportid()
  {
    return isset($_SESSION['esportid']) ? $_SESSION['esportid'] : 0;
  }
  /*
  *Converts the UTC/GMT UNIX standard epoch time to the user specific time zone formatted date. 
  */
  protected function get_local_date($epoch, $format = 'F j, Y')
  {
    $date = new DateTime("@$epoch", new DateTimeZone($this->TIMEZONE_DEFAULT));
    if($_SESSION['user']) {
      $date->setTimezone(new DateTimeZone($_SESSION['user']['timezone']));
    }
    return $date->format($format);
  }

  /*
  *Converts the UTC/GMT UNIX standard epoch time to the user specific time zone formatted date and time.
  */
  protected function get_local_datetime($epoch, $format='F j, Y H:i:s')
  {
    $date = new DateTime("@$epoch", new DateTimeZone($this->TIMEZONE_DEFAULT));
    if($_SESSION['user']) {
      $date->setTimezone(new DateTimeZone($_SESSION['user']['timezone']));
    }
    return $date->format($format);
  }

  protected function get_default_epoch($date)
  {
    date_default_timezone_set($_SESSION['user']['timezone']);
    $epoch = strtotime($date);
    $defdate = new DateTime("@$epoch",new DateTimeZone($_SESSION['user']['timezone']));
    $defdate->setTimezone(new DateTimeZone($this->TIMEZONE_DEFAULT));
    date_default_timezone_set($this->TIMEZONE_DEFAULT);
    return $defdate->getTimestamp();;
  }

  protected function show_404()
  {
    return;
  }

  /**
   * Hash a password using the user's unique salt.
   */
  public function password_hash($password)
  {
    // append the salt to thwart rainbow tables
    return sha1($password . $this->get_salt());
  }

  protected function get_salt()
  {
    return "LF98af2kF4K2kjL!dB";
  }

}
