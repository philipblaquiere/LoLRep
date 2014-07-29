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
        $this->load->model('match_model');
    }

    public function index()
    {
        $this->require_login();
        $data['player'] = $this->get_player();
        $data['is_logged_in'] = $this->is_logged_in();
        $this->load->library('lol_api');

        $player = $this->get_player();
        $teamids = $player['teams'];

        $params = array('teamids' => $teamids, 'esportid' => $this->get_esportid(), 'playerid' => $player['playerid'], 'region' =>$player['region']);
        $this->load->library('match_updater',$params);

       
        //$matches = $this->match_model->get_matches(array('03a54237-b5b8-587d-9dec-33331bf2b7dc','b719f12d-91e3-5229-a553-849918807849'),$this->get_esportid());
        $recent_matches = $this->match_updater->update();
        print_r($recent_matches);

        $this->load->view('include/header', $data);
        $this->load->view('profile_header', $data);
        $this->load->view('include/navigation', $data);
        $this->load->view('profile', $data);
        $this->load->view('include/footer');
    }
}