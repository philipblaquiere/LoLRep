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

   public function login() {
    if ($this->is_logged_in()) {
      redirect('home', 'location');
    }
    $data = array('page_title' => 'Sign In');
    $this->view_wrapper('user/sign_in', $data);
  }

 
  public function register() {
    //$this->require_not_login();
    //$countries = $this->country_model->get_supported_countries();
    //$supported_countries = array();
    //$supported_countries[''] = '';
    //foreach($countries as $country){
    //  $supported_countries[$country->countryid] = $country->country;
    //}

    $data = array(
      'page_title' => 'Register',
      //'supported_countries' => $country_options,
    );

    $this->view_wrapper('user/register', $data);
  }

  public function registration_submit() {
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
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
    $this->form_validation->set_rules('password1', 'Password1', 'required');
    $this->form_validation->set_rules('password2', 'Password2', 'required');
    $this->form_validation->set_rules('countryid', 'Country', 'required');
    $this->form_validation->set_rules('provincestateid', 'Province/State', 'required');
    $this->form_validation->set_rules('regionid', 'Region', 'required');

    if($this->form_validation->run() == FALSE){
      $this->system_message_model->set_message('Registration failed. Please retry and ensure you fill all boxes properly. If error persists, please contact site administrator.', MESSAGE_ERROR);
      redirect('user/register', 'location');
    }
    elseif($plain_password != $plain_password2 ){
      $this->system_message_model->set_message('Passwords do not match. Please try again.', MESSAGE_ERROR);
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

  public function select_esport() {
    $this->require_login();
    $esports = $this->esport_model->get_all_esports();
    $data['esports'] = $esports;
    $this->view_wrapper('user/select_esport',$data);
  }

  public function register_LoL() {
    $this->require_login();
    $this->view_wrapper('user/register_LoL');
  }

  public function create_summoner() {
    $this->require_login();
    if(!$_SESSION['summoner']) {
      //global object not present, an error has occured while checking rune pages or while redirecting here from JQuery
      $this->system_message_model->set_message('Error: No Summoner has been found. Cannot complete registration', MESSAGE_INFO);
      redirect('user/register_LoL', 'location');
    }
    else {
      //valid summoner, create summoner and redirect to home page.
      $this->lol_model->create_summoner($_SESSION['uid'], $_SESSION['summoner']);
      $this->system_message_model->set_message($_SESSION['summoner']['name'] . ', you have successfully linked your League of Legends account!', MESSAGE_INFO);
      unset($_SESSION['summoner']);
      $this->view_wrapper('home');
    }
  }

  public function teams() {
    $this->require_login();
    $this->view_wrapper('user/teams');
  }

  public function create_team() {
    $this->require_login();
    $esports = $this->esport_model->get_all_esports();
    $data['esports'] = $esports;
    $this->view_wrapper('user/create_team', $data);
  }

  public function create_team_submit() {
    $this->require_login();

    //Validation on input (requires that all fields exist)
    $this->load->helper(array('form', 'url'));
    $this->load->library('form_validation');


    $this->form_validation->set_rules('esportid', 'ESport', 'required');
    $this->form_validation->set_rules('teamname', 'Team Name', 'trim|required|xss_clean');
    $this->form_validation->set_rules('make_captain', 'Make Captain', 'required');

    $team['name'] = $this->input->post('teamname');
    $team['esportid'] = $this->input->post('esportid');
    $make_captain = $this->input->post('make_captain');

    if($this->form_validation->run() == FALSE){
      $this->system_message_model->set_message('Team Creation failed. ', MESSAGE_ERROR);
      redirect('user/create_team', 'location');
    }
    $existing_team = $this->team_model->get_team_by_name($team['name'], $team['esportid']);
    if($existing_team) {
      //team already exists.
      $this->system_message_model->set_message('A team with an identical name already exists.', MESSAGE_ERROR);
      redirect('user/create_team', 'location');
    }
    else {
      //create team with logged in user as captain
      if($make_captain) {
        $captain = $_SESSION['user'];
        $this->team_model->create_team($team,$captain);
        $this->system_message_model->set_message($team['name'] . ' has been created, add people to your team' , MESSAGE_INFO);
        redirect('home', 'location');
      }
      else {
        //make team without captain
        $captain = $_SESSION['user'];
        $this->team_model->create_team($team,$captain);
        $this->system_message_model->set_message($team['name'] . ' has been created, add people to your team' , MESSAGE_INFO);
        redirect('home', 'location');
      }
    }
  }

  public function join_team() {
    $this->require_login();
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