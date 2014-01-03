<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller{
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

  public function home()
  {
    $this->view_wrapper('home');
  }
	/**
	 * User Page for this controller.
	 */
	public function index(){
    $this->register();
	}

  public function pending_validation() {
     $this->view_wrapper('user/pending_validation');
  }

  public function validate_user($key, $uid) {
    if($this->user_model->validate_user($key, $uid))
    {
      //user validation succeeded, proceed with asking user to sign in to continue
      $this->view_wrapper('user/user_validated');
    }
    else
    {
      //user validation failed
    }
  }

  

/*
  

  public function profile() {
    
  }

  public function modify_profile() {
    
  }

  public function modify_profile_submit() {
  
  }

  public function change_password() {
   
  }

  public function change_password_submit() {
  
  }

  public function password_retrieval() {

  }

  public function password_retrieval_submit() {

  }*/
}