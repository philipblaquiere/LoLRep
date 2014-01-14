<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trade extends MY_Controller{
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
        $this->load->model('team_model');
        $this->load->model('lol_model');
        $this->load->model('trade_lol_model');
    }

    public function lol() {
        $this->require_login();
        $esport = 1; //league of legends id
        $team = $this->team_model->get_team_by_captainid($_SESSION['user']['UserId'], $esport);

        if($team) {
            //user is captain, proceed
            $data['team'] = $team;
            $data['team_lol'] = $this->team_model->get_team_lol($team['teamid']);
            $this->view_wrapper('team_trade_lol',$data);
        }
        else {
            //unauthorized accessm re-route home
            $this->system_message_model->set_message("Unauthorized access : You must be team captain to trade." , MESSAGE_INFO);
            redirect('home','refresh');
        }
    }
}