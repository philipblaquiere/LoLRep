<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
  {
    parent::__construct();
    $this->load->model('system_message_model');
    $this->load->model('player_model');
  }
  public function index()
  {
    $data['player'] = $this->get_player();
    $data['is_logged_in'] = $this->is_logged_in();
    $this->view_wrapper('profile', $data);
  }

  public function create()
  {
    $this->require_login();
    switch ($this->get_esportid())
    {
      case '1':
        $this->_create_lol();
        break;
      
      default:
        # code...
        break;
    }

  }

  private function _create_lol()
  {
    $player = $this->get_player();
    
    //check for registered in case a currently registered player somehow accesses the create controller.
    if(empty($player) || array_key_exists('registered', $_SESSION['player']))
    {
      //global object not present, an error has occured while checking pages or while redirecting herrune e from JQuery
      $this->system_message_model->set_message('Error: No Summoner has been found. Cannot complete registration', MESSAGE_INFO);
      redirect('add_esport', 'location');
    }
    //valid summoner, create summoner and redirect to home page.
    $this->player_model->create($this->get_userid(), $player, $this->get_esportid());
    $this->system_message_model->set_message($player['player_name'] . ', you have successfully linked your League of Legends account!', MESSAGE_INFO);
    redirect('home','refresh');
    
  }
}