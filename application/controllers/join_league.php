<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Join_league extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
    private $UNIX_DAY = 86400;

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
        $this->load->model('league_model');
        $this->load->model('season_model');
    }

    public function index()
    {
    }

    public function join($leagueid)
    {
        
    }
}