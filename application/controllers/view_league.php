<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_league extends MY_Controller{
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
        $this->load->model('league_model');
        $this->load->model('season_model');
    }

    public function index() {
        $data['esports'] = $this->esport_model->get_all_esports();
        $season = $this->season_model->get_new_season();
        $data['leagues_info'] = $this->league_model->get_all_leagues_detailed($season['seasonid']);
        $data['league_teams'] = $this->league_model->get_active_league_teams();
        $this->view_wrapper('view_league.php', $data);
    }
}
