<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('system_message_model');
        $this->load->model('country_model');
        $this->load->model('ip_log_model');
    }
    
    public function index() {
      //$this->require_not_login();
      //$countries = $this->country_model->get_supported_countries();
      //$supported_countries = array();
      //$supported_countries[''] = '';
      //foreach($countries as $country){
      //  $supported_countries[$country->countryid] = $country->country;
      //}
      $this->require_not_login();

      //Validation on input (requires that all fields exist)
      //$this->load->helper(array('form', 'url'));
      $this->load->library('form_validation');

      $this->form_validation->set_rules('fname', 'First Name', 'trim|required|xss_clean');
      $this->form_validation->set_rules('lname', 'Last Name', 'trim|required|xss_clean');
      $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_unique_email');
      $this->form_validation->set_rules('password1', 'Password', 'required|xss_clean|callback_password_match');
      $this->form_validation->set_rules('password2', 'Re-Password', 'required|xss_clean');
      $this->form_validation->set_rules('countryid', 'Country', 'required');
      $this->form_validation->set_rules('provincestateid', 'Province/State', 'required');
      $this->form_validation->set_rules('regionid', 'Region', 'required');

      if($this->form_validation->run() == FALSE){
        $this->view_wrapper('user/register');
      } 

      else{
        $ip = $this->input->ip_address();

        $user = $this->input->post();
        $user['salt'] = $this->user_model->_generate_salt();
        $user['password'] = $this->user_model->_password_hash($user['password1'], $user['salt']);
        //Save user object and get key to send to user email.
        $newuser = $this->user_model->create($user);

        /*
        $email = "Hello, click this link to valide your account: " . site_url("register/validate_user/". $newuser['key'] . "/" . $newuser['uid']);

        $this->load->helper('email');
        send_email($email);*/

        $this->system_message_model->set_message('We sent you a confirmation email, follow the link to complete your registration.', MESSAGE_INFO);

        redirect('user/pending_validation', 'refresh');
      }

      $data = array(
        'page_title' => 'Register',
        //'supported_countries' => $country_options,
      );

      //$this->view_wrapper('user/register', $data);
    }

    public function password_match($pw1) {
      $pw2 = $this->input->post('password2');

      if($pw2 != $pw1){
        $this->form_validation->set_message('password_match', 'The passwords do not match.');
        return false;
      }
      else{
        return true;
      }
    }

    public function unique_email($email) {
      $email = strtolower($email);
      $user = $this->user_model->get_by_email($email);
      if($user) {
        $this->form_validation->set_message('unique_email', 'That email is already registered with our website, choose another one.');
        return false;
      }
      else {
        return true;
      }
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
}