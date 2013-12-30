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
        $this->load->model('esport_model');
        $this->load->model('lol_model');
        $this->load->model('team_model');
    }

    public function registration_submit() {
        
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

      $ip = $this->input->ip_address();

      $user->fname = $this->input->post('fname');
      $user->lname = $this->input->post('lname');
      $user->email = strtolower($this->input->post('email'));
      $plain_password = $this->input->post('password1');
      $plain_password2 = $this->input->post('password2');
      $user->countryid = $this->input->post('countryid');
      $user->provincestateid = $this->input->post('provincestateid');
      $user->regionid = $this->input->post('regionid');
      $user->salt = $this->user_model->_generate_salt();
      $user->password = $this->user_model->_password_hash($plain_password, $user->salt);

      //Validation on input (requires that all fields exist)
      $this->load->helper(array('form', 'url'));
      $this->load->library('form_validation');

      $this->form_validation->set_rules('fname', 'First Name', 'trim|required|xss_clean');
      $this->form_validation->set_rules('lname', 'Last Name', 'trim|required|xss_clean');
      $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_unique_email');
      $this->form_validation->set_rules('password1', 'Password1', 'required|callback_password_match');
      $this->form_validation->set_rules('password2', 'Password2', 'required');
      $this->form_validation->set_rules('countryid', 'Country', 'required');
      $this->form_validation->set_rules('provincestateid', 'Province/State', 'required');
      $this->form_validation->set_rules('regionid', 'Region', 'required');

      if($this->form_validation->run() == FALSE){
        $this->system_message_model->set_message('Registration failed. Please retry and ensure you fill all boxes properly. If error persists, please contact site administrator.', MESSAGE_ERROR);
        redirect('user/register', 'location');
      }
      elseif($this->user_model->get_by_email($user->email))
      {
        $this->system_message_model->set_message('That email is already registered with our website, choose another one.', MESSAGE_ERROR);
        redirect('user/register', 'location');
      }
      /*elseif($this->user_model->get_by_email($user->email) != FALSE){
        $this->system_message_model->set_message('Registration failed. Email address is already registered. Please choose another address.', MESSAGE_ERROR);
        redirect('user/register', 'location');
      }
      elseif($this->ip_log_model->get_by_ip($ip)) {
        $this->system_message_model->set_message("Your registration has failed. Your IP address {$ip} has already registered an account in the past " . IP_TTL . " seconds. Please wait and try again soon.", MESSAGE_ERROR);
        redirect('user/register', 'location');
      }*/
      else{

        //Save user object and get key to send to user email.
        $newuser = $this->user_model->create($user);
        //Log IP
        //$this->ip_log_model->log_ip($ip);

        //Automatically grant 'user' role
        //$role = $this->user_roles_model->factory();
        //$role->uid = $user->uid;
        //$role->rid = 1;
        //$this->user_roles_model->save($role);

        $email = "Hello, click this link to valide your account: http://" . site_url() . "user/validate_user/". $newuser['key'] . "/" . $newuser['uid'];

        //if(EMAIL_FLAG){
          $this->load->helper('email');
          send_email($email);
        //}
        //else{
        //  $_SESSION['email'] = $email;
        //}

        $this->system_message_model->set_message('We sent you a confirmation email, follow the link to complete your registration.', MESSAGE_INFO);
        redirect('user/pending_validation', 'location');
      }

      $data = array(
        'page_title' => 'Register',
        //'supported_countries' => $country_options,
      );

      $this->view_wrapper('user/register', $data);
    }

    public function password_match($pw1){
      $pw2 = $this->input->post('password2');

      if($pw2 != $pw1){
        $this->form_validation->set_message('password_match', 'The passwords do not match.');
        return false;
      }
      else{
        return true;
      }
    }
}