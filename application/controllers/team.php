<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends MY_Controller{
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
    }
    public function teams() {
        $this->require_login();
        $this->view_wrapper('user/teams');
    }

    public function join_team() {
        $this->require_login();
    }
}