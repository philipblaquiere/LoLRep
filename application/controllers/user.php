<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
    parent::__construct();
    $this->load->model('user_model');
  }

	/**
	 * User Page for this controller.
	 */
	public function index(){
    $this->login();
	}

   public function login() {
    if ($this->is_logged_in()) {
      redirect('user/profile', 'location');
    }
    $data = array('page_title' => 'Open A Session');
    $this->view_wrapper('user/login', $data);
  }

  public function validate() {

  }

  public function logout() {

  }

  public function register() {
    $this->require_not_login();
    $countries = $this->country_model->get_all();
    $country_options = array();
    $country_options[''] = '';
    foreach($countries as $country){
      $country_options[$country->countryid] = $country->country;
    }

    $data = array(
      'page_title' => 'Register',
      'country_options' => $country_options,
    );
    $this->view_wrapper('user/register', $data);
  }

  public function registration_submit() {
    
  }

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

  }
  public function register_lol() {
    $this->load->view('header');
    $this->load->view('user/register_lol');
    $this->load->view('footer');
  }