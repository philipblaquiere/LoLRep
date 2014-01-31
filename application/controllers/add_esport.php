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
		$this->load->model('lol_model');
		$this->load->model('riotapi_model');
	}
	public function index()
	{
		$this->require_login();
		$esports = $this->esport_model->get_all_esports();
		$data['esports'] = $esports;
		$this->view_wrapper('user/add_esport',$data);
	}

	public function register_LoL()
	{
		$this->require_login();
		$summonername = $this->lol_model->get_summonername_from_uid($_SESSION['user']['UserId']);
		if($summonername)
		{
			$data['esports'] = $this->esport_model->get_all_esports();
			$this->system_message_model->set_message("You have already registered a League of Legends account : " . $summonername['SummonerName']  , MESSAGE_INFO);
			$this->view_wrapper('user/add_esport', $data);
		}
		else
		{
			$this->view_wrapper('user/register_LoL');
		}
	}

	public function create_summoner() 
	{
		$this->require_login();
		if(!$_SESSION['summoner']) 
		{
			//global object not present, an error has occured while checking rune pages or while redirecting here from JQuery
			$this->system_message_model->set_message('Error: No Summoner has been found. Cannot complete registration', MESSAGE_INFO);
			redirect('user/register_LoL', 'location');
		}
		else 
		{
			 //valid summoner, create summoner and redirect to home page.
			$_SESSION['summoner']['summonerrank'] = $this->riotapi_model->getLeague($_SESSION['summoner']['id']);
			
			$this->lol_model->create_summoner($_SESSION['uid'], $_SESSION['summoner']);
			$this->esport_model->register_user_lol($_SESSION['uid']);
			$this->system_message_model->set_message($_SESSION['summoner']['name'] . ', you have successfully linked your League of Legends account!', MESSAGE_INFO);
			unset($_SESSION['summoner']);
			$this->view_wrapper('home');
		}
	}
}
