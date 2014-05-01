<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sign_in extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
  {
    parent::__construct();
    $this->load->model('user_model');
    $this->load->model('system_message_model');
    $this->load->model('country_model');
    $this->load->model('ip_log_model');
    $this->load->model('esport_model');
    $this->load->model('player_model');
    $this->load->model('team_model');
    $this->load->model('league_model');
    $this->load->model('banned_model');
    $this->load->model('season_model');
  }

  public function index()
  {
    $this->sign_in();
  }

  public function login()
  {
    if ($this->is_logged_in())
    {
      redirect('home', 'location');
    }
    $data = array('page_title' => 'Sign In');
    $this->view_wrapper('user/sign_in', $data);
  }

  public function sign_in()
  {
    $this->require_not_login();

    $this->load->library('form_validation');

    $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
    $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

    if($this->form_validation->run() == FALSE)
    {
        $this->view_wrapper('sign_in');
    } 
    else
    {
      //get sign in form data
      $email = $this->input->post('email');
      $password = $this->input->post('password');

      $user = $this->user_model->get_by_email($email);
      if(!$user)
      {
        $this->system_message_model->set_message('There is an error in your email or password', MESSAGE_INFO);
        $this->view_wrapper('sign_in');
      }

      else if($user['validated'] == 0)
      {
        //user did not validate themselves, prompt them to resend the validation email.
        $this->system_message_model->set_message('Hey! This account was never validated. Check your emails for an email we sent you!', MESSAGE_INFO);
        $this->view_wrapper('sign_in');
      }
      else if($this->_validate_password($user,$password)) 
      {
        $banned_user = $this->banned_model->get_by_userid($user['userid']);

        if($banned_user)
        {
          $this->system_message_model->set_message('You have been banned from our website. Reason : ' . $banned_user['reason'], MESSAGE_INFO);
          $this->view_wrapper('sign_in');
          return;
        }
        //user validated, proced with login
        $player = $this->player_model->get_player_by_email($user['email'], 1);
        $this->set_current_user($user);
        $this->set_player($player);
        $this->user_model->log_login($user['userid']);
        $this->system_message_model->set_message('Welcome, ' . $user['first_name'], MESSAGE_INFO);
        redirect('home', 'refresh');
      }
      else
      {
        $this->system_message_model->set_message('There is an error in your email or password', MESSAGE_INFO);
        $this->view_wrapper('sign_in');
      }
    }
  }
  
  public function sign_out()
  {
    $this->require_login();
    $this->destroy_session();
    $data = array('page_title' => 'Sign out successful');
    $this->system_message_model->set_message('Sign out successful', MESSAGE_INFO);
    redirect('home', 'location');
  }

  private function _validate_password($user,$password) {
    if(!$password || !$user['email'])
      return false;
    return $user['password'] === $this->password_hash($password);
  }
}