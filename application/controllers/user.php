<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
    parent::__construct();
  }

	/**
	 * User Page for this controller.
	 */
	public function index(){
    $this->login();
	}

  public function register_lol() {
    $this->load->view('header');
    $this->load->view('register/league_of_legends');
    $this->load->view('footer');
  }

  public function login() {
  }

  public function validate() {

  }

  public function logout() {

  }

  public function register() {

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
