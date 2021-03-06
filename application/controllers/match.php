<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match extends MY_Controller
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
        $this->load->model('lol_model');
    }

    public function index($matchid)
    {
        $match_details = $this->match_model->get_match_details($matchid);
    }
}