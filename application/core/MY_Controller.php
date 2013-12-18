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
    //$this->load->library('database_layer');
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('date');
    //load all models
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
   * Convinience function to get the ID of the currently logged in user.
   */
  protected function get_current_user() {
    return isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
  }

  protected function set_current_user($uid) {
    $_SESSION['uid'] = $uid;
    $_SESSION['user'] = $this->user_model->get_by_uid($uid);
  }

  protected function destroy_session() {
    unset($_SESSION['uid']);
    unset($_SESSION['user']);
    unset($_SESSION['last_login_time']);
  }

  /**
   * Verifies the current user's session and redirects to the login form if the
   * user has not authenticated.
   */
  protected function require_login() {
    if (!$this->is_logged_in()) {
      $this->system_message_model->set_message('Please login to access this page.', MESSAGE_WARNING);
      redirect('user/sign_in', 'location');
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
}
