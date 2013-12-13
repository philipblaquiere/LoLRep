<?php
/**
 * @file
 * Extends the base controller to load the PHP session upon construction. Also
 * provides a convienient way to render a view and check if the user is logged
 * in.
 */
class MY_Controller extends CI_Controller  {
  public function __construct() {
    parent::__construct();
    session_start();
    $this->load->library('database_layer');
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('date');
    $this->load->model('system_message_model');
    $this->load->model('event_model');
    $this->load->model('conference_model');
    $this->load->model('user_roles_model');
    $this->load->model('user_position_model');
    $this->load->model('user_model');
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
    $data['is_admin_user'] = $this->is_admin_user();
    $this->load->view('include/header', $data);

    $data['conferences'] = $this->conference_model->get_all();
    $data['current_conference'] = array();
    $data['current_events'] = array();
    if ($current_conference = $this->get_current_conference()) {
      $data['current_conference'] = $this->conference_model->get($current_conference);
      $data['current_events'] = $this->event_model->get_by_conference($current_conference);
    }
    if ($data['is_logged_in']){
      $data['user'] = $this->user_model->get($this->get_current_user());
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
    return isset($_SESSION['uid']);
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
   * Convinience function to get the student ID of the currently logged in user.
   */
  protected function get_current_user() {
    return isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
  }

  protected function set_current_user($uid) {
    $_SESSION['uid'] = $uid;
  }

  protected function set_current_conference($cid) {
    $_SESSION['cid'] = $cid;
  }

  protected function get_current_conference() {
    return isset($_SESSION['cid']) ? $_SESSION['cid'] : 0;
  }

  protected function destroy_session() {
    unset($_SESSION['uid']);
    unset($_SESSION['cid']);
    unset($_SESSION['last_login_time']);
  }

  /**
   * Verifies the current user's session and redirects to the login form if the
   * user has not authenticated.
   */
  protected function require_login() {
    if (!$this->is_logged_in()) {
      $this->system_message_model->set_message('Please login to access this page.', MESSAGE_WARNING);
      redirect('user/login', 'location');
      die();
    }
  }

  /**
   * Verifies the current user's session and redirects to the login form if the
   * user has not authenticated.
   */
  protected function require_admin() {
    if (!$this->is_logged_in() || !$this->is_admin_user()) {
      //$this->system_message_model->set_message('Administrator access is required to access this page.', MESSAGE_WARNING);
      show_error('Access denied: you must be logged in as an administrator to view this content.', 403);
    }
  }

  /**
  * Verifies that client is not logged in, and if so, it logs them out but allows them to view
  * the originally requested page (ie Registration)
  */
  protected function require_not_login(){
    if ($this->is_logged_in()){
      $this->system_message_model->set_message('You have been automatically logged out in order to view this page.', MESSAGE_WARNING);
      $this->destroy_session();
    }
  }

  /**
   * Verifies the user is admin OR a member of the program chair.
   */
  protected function is_program_chair($eid) {
    if ($uid = $this->get_current_user()) {
      return $this->is_admin_user() || in_array(2, $this->user_position_model->get_roles($eid, $uid)); // Program Chair
    }
    return FALSE;
  }

  protected function require_program_chair($eid) {
    if (!$this->is_program_committee($eid)) {
      show_error('Access denied: you must be logged in as a program chair to view this content.', 403);
    }
  }

  /**
   * Verifies the user is admin OR program chair OR a member of the program committee.
   */
  protected function is_program_committee($eid) {
    if ($uid = $this->get_current_user()) {
      return $this->is_program_chair($eid) || in_array(3, $this->user_position_model->get_roles($eid, $uid)); // Program Chair
    }
    return FALSE;
  }

  protected function require_program_committee($eid) {
    if (!$this->is_program_committee($eid)) {
      show_error('Access denied: you must be logged in as a program committee member to view this content.', 403);
    }
  }

}
