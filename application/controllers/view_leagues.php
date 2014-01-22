<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_leagues extends MY_Controller{
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
        $this->require_login();
        $data['esports'] = $this->esport_model->get_all_esports();
        $season = $this->season_model->get_new_season();
        $leagues_info = $this->league_model->get_all_leagues_detailed($season['seasonid']);
        $league_teams = $this->league_model->get_active_league_teams($_SESSION['esportid']);
        $captain_team = $this->team_model->get_team_by_captainid($_SESSION['user']['UserId'], $_SESSION['esportid']);
        $current_team = $this->team_model->get_team_by_uid($_SESSION['user']['UserId'], $_SESSION['esportid']);
        $current_league = $this->league_model->get_current_league_by_teamid($current_team['teamid']);
        
        foreach ($leagues_info as $league_info) {
            //Check if user can join league, can join if 
            /*  season is new
            *   isn't already part of the league
            *   league aint full   
            *   league aint invite only
            *   user is captain
            *   hasn't already changed league today
            */
            $leagues_info[$league_info['league_name']]['num_teams'] = array_key_exists($league_info['league_name'], $league_teams) ? count($league_teams[$league_info['league_name']]['teams']) : 0 ;

            if($current_league && $current_league['leagueid'] == $leagues_info[$league_info['league_name']]['leagueid']) {
                $leagues_info[$league_info['league_name']]['can_join'] = 0;
                $leagues_info[$league_info['league_name']]['join_status'] = "Current";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "You are currently part of this league";
            }
            else if(!$captain_team) {
                //User is not a captain of any team
                $leagues_info[$league_info['league_name']]['can_join'] = 0;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "You must be captain of your team to join this league";
            }
            else if($league_info['invite'] == 1) {
                $leagues_info[$league_info['league_name']]['can_join'] = 0;
                $leagues_info[$league_info['league_name']]['join_status'] = "Invite Only";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "This league is invite only";
            }
            else if($leagues_info[$league_info['league_name']]['num_teams'] == $league_info['max_teams']) {
                $leagues_info[$league_info['league_name']]['can_join'] = 0;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "This league is full";
            }
            
            else {
                $leagues_info[$league_info['league_name']]['can_join'] = 1;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "Join this league";
            }
        }
        $data['league_teams'] = $league_teams;
        $data['leagues_info'] = $leagues_info;
        $data['current_league'] = $current_league;
        $data['max_league_count'] = 20;
        
        $this->view_wrapper('view_leagues', $data);
    }

    public function view($leagueid) {
        $this->require_login();
        $teams = $this->team_model->get_teams_byleagueid($leagueid,$_SESSION['esportid']);
        if(!$teams) {
            $teams['teams'] = array();
        }
        $league = $this->league_model->get_league_byid($leagueid);

        $data['teams'] = $teams;
        $data['league'] = $league;
        $this->view_wrapper('view_league', $data);

    }
}
