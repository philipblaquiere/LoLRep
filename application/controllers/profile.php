<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends MY_Controller
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
        $this->load->model('team_model');
    }

    public function index()
    {
        $this->require_login();
        $data['player'] = $this->get_player();
        $data['is_logged_in'] = $this->is_logged_in();
        $this->load->library('lol_api');
        $player = $this->get_player();
        $this->lol_api->getRecentGames($player['playerid']);

        $this->load->view('include/header', $data);
        $this->load->view('profile_header', $data);
        $this->load->view('include/navigation', $data);
        $this->load->view('profile', $data);
        $this->load->view('include/footer');
    }
}