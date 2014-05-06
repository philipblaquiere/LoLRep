<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends MY_Controller{
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
    $this->load->model('team_model');
    $this->load->model('riotapi_model');
    $this->load->model('trade_lol_model');
    $this->load->model('player_model');
  }

  public function create()
  {
    $this->require_login();
    switch ($this->get_esportid()) {
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
    if($this->player_exists())
    {
      //global object not present, an error has occured while checking pages or while redirecting herrune e from JQuery
      $this->system_message_model->set_message('Error: No Summoner has been found. Cannot complete registration', MESSAGE_INFO);
      redirect('add_esport', 'location');
    }
    else
    {
      //valid summoner, create summoner and redirect to home page.
      $player = $this->get_player();
      $player['rank'] = $this->riotapi_model->getLeague($player['playerid']);
      
      $this->player_model->create($this->get_userid(), $player , $this->get_esportid());
      $this->system_message_model->set_message($player['player_name'] . ', you have successfully linked your League of Legends account!', MESSAGE_INFO);
      redirect('home','refresh');
    }
  }
}