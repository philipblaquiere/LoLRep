<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Players extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
  {
    parent::__construct();
    $this->load->model('system_message_model');
    $this->load->model('player_model');
    $this->load->model('statistics_model');
  }

  function _remap($playerid)
  {
    $this->index($playerid);
  }

  public function index($playerid)
  {
    $player = $this->player_model->get_player($playerid,$this->get_esportid());
    print_r($_SESSION['player']);
    $data['player'] = $player;
    $data['banner']['title_big'] = $player['player_name'];
    $data['is_logged_in'] = $this->is_logged_in();
    $this->view_wrapper('profile',$data);
  }