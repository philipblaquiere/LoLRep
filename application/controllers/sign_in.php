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

  public function index() {
    $this->sign_in();
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


    $this->load->library('form_validation');

    $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
    $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

    if($this->form_validation->run() == FALSE){
        $this->view_wrapper('user/sign_in');
    } 
    else {
      //get sign in form data
      $email = $this->input->post('email');
      $password = $this->input->post('password');

      $user = $this->user_model->get_by_email($email);

      if(!$user) {
        $this->system_message_model->set_message('There is an error in your email or password', MESSAGE_INFO);
        $this->view_wrapper('user/sign_in');
      }
      
      else if($user['validated'] == 0) {
        //user did not validate themselves, prompt them to resend the validation email.
        $this->system_message_model->set_message('Hey! This account was never validated. Check your emails for an email we sent you!', MESSAGE_INFO);
        $this->view_wrapper('user/sign_in');
      }
      else if($this->user_model->validate_password($user,$password)) {
        //uservalidated, proced with login
        $this->set_current_user($user['UserId']);

        $this->user_model->log_login($user['UserId']);
        $this->system_message_model->set_message('Welcome, ' . $user['firstname'], MESSAGE_INFO);
        redirect('home', 'refresh');
      }
      else {
        $this->system_message_model->set_message('There is an error in your email or password', MESSAGE_INFO);
        $this->view_wrapper('user/sign_in');
      }
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