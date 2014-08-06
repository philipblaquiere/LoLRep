<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
  {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('system_message_model');
        $this->load->model('banned_model');
  }
  
  public function index()
  {
    $this->require_not_login();

    //Validation on input (requires that all fields exist)
    $this->load->library('form_validation');

    $this->form_validation->set_rules('fname', 'First Name', 'trim|required|xss_clean');
    $this->form_validation->set_rules('lname', 'Last Name', 'trim|required|xss_clean');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email|callback_unique_email');
    $this->form_validation->set_rules('password1', 'Password', 'required|xss_clean|callback_password_match');
    $this->form_validation->set_rules('password2', 'Re-Password', 'required|xss_clean');
    $this->form_validation->set_rules('timezones', 'Time Zone', 'required');

    if($this->form_validation->run() == FALSE)
    {
      $this->view_wrapper('register');
    } 

    else
    {
      $ip = $this->input->ip_address();

      $user = $this->input->post();
      $user['password'] = $this->password_hash($user['password1']);
      //Save user object and get key to send to user email.
      $newuser = $this->user_model->create($user);

      $this->system_message_model->set_message('We sent you a confirmation email, follow the link to complete your registration.', MESSAGE_INFO);

      $this->view_wrapper('pending_validation');
    }
  }

  public function password_match($pw1)
  {
    $pw2 = $this->input->post('password2');

    if($pw2 != $pw1)
    {
      $this->form_validation->set_message('password_match', 'The passwords do not match.');
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }

  public function unique_email($email)
  {
    $email = strtolower($email);
    $user = $this->user_model->get_by_email($email);
    if($user)
    {
      $this->form_validation->set_message('unique_email', 'That email is already registered with our website, choose another one.');
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }

  public function validate_user($key, $uid)
  {
    if($this->user_model->validate_user($key, $uid))
    {
      //user validation succeeded, proceed with asking user to sign in to continue
      $this->view_wrapper('user/user_validated');
    }
  }

  public function is_banned($email) 
  {
    $banned_user = $this->banned_model->get_byemail($email);
    if($banned_user)
    {
      //user with that email has been banned
      $this->system_message_model->set_message('This email has been banned from our website.', MESSAGE_ERROR);
      $this->form_validation->set_message('not_banned', 'This email has been banned from our website.');
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }
}