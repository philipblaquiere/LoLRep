<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Add_esport extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('system_message_model');
		$this->load->model('esport_model');
		$this->load->model('player_model');
		$this->load->model('riotapi_model');
	}
	public function index()
	{
		$this->require_login();
		switch ($this->get_esportid()) 
		{
			case '1':
				# Register League of Legends
				$this->register_lol();
				break;
			
			default:
				# code...
				break;
		}
	}

	public function register_lol()
	{
		$this->require_login();
		if($this->player_exists())
		{
			$player = $this->get_player();
			$this->system_message_model->set_message("You have already registered a League of Legends account : " . $player['player_name']  , MESSAGE_INFO);
			redirect('home', 'refresh');
		}
		else
		{
			$this->view_wrapper('register_lol');
		}
	}
}
