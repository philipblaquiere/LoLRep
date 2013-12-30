<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sign_in extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
    parent::__construct();
    $this->load->model('user_model');
    $this->load->model('system_message_model');
    $this->load->model('country_model');
    $this->load->model('ip_log_model');
    $this->load->model('esport_model');
    $this->load->model('lol_model');
    $this->load->model('team_model');
  }

  public function login() {
    if ($this->is_logged_in()) {
      redirect('home', 'location');
    }
    $data = array('page_title' => 'Sign In');
    $this->view_wrapper('user/sign_in', $data);
  }

  public function sign_in() {
    $this->require_not_login();

    //get sign in form data
    $email = $this->input->post('email');
    $password = $this->input->post('password');

    $user = $this->user_model->get_by_email($email);
    
    if (!$user){
      $this->system_message_model->set_message('Uh oh...There appears to be an error in your email or password', MESSAGE_ERROR);
      $this->view_wrapper('user/sign_in');
    }
    else if ($this->user_model->validate_password($user,$password)) {
      if($user['validated'] == 0) {
        //user did not validate themselves, prompt them to resend the validation email.
        $this->system_message_model->set_message('Hey! This account was never validated. Check your emails for an email we sent you!', MESSAGE_INFO);
        $this->view_wrapper('user/sign_in');
      }
      else {
        $this->set_current_user($user['UserId']);

        $this->user_model->log_login($user['UserId']);
        $this->system_message_model->set_message('Welcome, ' . $user['firstname'], MESSAGE_INFO);
        redirect('home', 'location');
        //$this->view_wrapper('home');
        //
        //last sign in logic goes here.
        //if(!$user->last_login_time) {
        //  $this->system_message_model->set_message('Welcome, ' . $user->fname . 'First time login', MESSAGE_INFO);
        //  $this->view_wrapper('home');
        //}
        //else {
        //  $_SESSION['last_login_time'] = $user->last_login_time;
        //  $this->system_message_model->set_message('Welcome, ' . $user->fname . '.', MESSAGE_INFO);
        //  $this->view_wrapper('home');
        //}
      }
    }
    else {
      //user authentication failed, redirect to login
      $this->system_message_model->set_message('Uh oh...There appears to be an error in your email or password', MESSAGE_ERROR);
      $this->view_wrapper('user/sign_in');
    }
  }
  
  public function sign_out() {
    $this->require_login();
    $this->destroy_session();
    $data = array('page_title' => 'Sign out successful');
    $this->system_message_model->set_message('Sign out successful', MESSAGE_INFO);
    redirect('home', 'location');
  }

}