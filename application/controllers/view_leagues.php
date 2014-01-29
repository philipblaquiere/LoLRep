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
        $this->load->model('match_model');
    }

    public function index() {
        $this->require_login();
        $data['esports'] = $this->esport_model->get_all_esports();
        $leagues_info = $this->league_model->get_all_leagues_detailed($_SESSION['esportid']);
        $league_teams = $this->league_model->get_active_league_teams($_SESSION['esportid']);
        $captain_team = $this->team_model->get_team_by_captainid($_SESSION['user']['UserId'], $_SESSION['esportid']);
        $user_current_team = $this->team_model->get_team_by_uid($_SESSION['user']['UserId'], $_SESSION['esportid']);
        if(!empty($user_current_team)) {
            $current_league = $this->league_model->get_current_league_by_teamid($user_current_team['teamid']);
        }
        else {
            //user isn't part of a team, let the user know that he can't join a league
            $this->system_message_model->set_message("You must be captain of your registered team to join a league!", MESSAGE_INFO);
        }
        
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

            if(empty($user_current_team)) {
                //User not part of a team
                $leagues_info[$league_info['league_name']]['can_join'] = 0;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "You need to be part of a registered team to join this league";
            }
            else if(!empty($current_league) && $current_league['leagueid'] == $leagues_info[$league_info['league_name']]['leagueid']) {
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
        $data['current_league'] = empty($current_league) ? array() : $current_league;
        $data['max_league_count'] = 20;
        
        $this->view_wrapper('view_leagues', $data);
    }

    public function start_season($seasonid)
    {
        $this->require_login();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('season_start_date', 'Season Start', 'required|callback_value_infuture');

        $leagueid = $this->input->post('leagueid');

        if($this->form_validation->run() == FALSE) {
            $this->system_message_model->set_message(validation_errors()  , MESSAGE_ERROR);
            $this->view($leagueid);
        }
        else
        {
            $league = $this->league_model->get_league_details($leagueid);
            $league_teams = $this->team_model->get_teams_byleagueid($leagueid,$_SESSION['esportid']);
            $start_date = $this->input->post('season_start_date');

            $this->load->library('schedule_maker');
            $this->schedule_maker->set_num_teams(count($league_teams['teams']));

            $this->schedule_maker->set_start_date($start_date);
            $this->schedule_maker->set_duration($league['season_duration']);
            $this->schedule_maker->set_matches($league['first_matches']);
            $schedule = $this->schedule_maker->generate_schedule();

            //Replace the teamporary teamids with actual teamid's in the schedule
            $teams = array();
            foreach ($league_teams['teams'] as $team) {
                array_push($teams, $team['teamid']);
            }
            $match_num = 0;
            foreach ($schedule as $match) {
                $schedule[$match_num]['teamaid'] = $teams[$match['teamaid']];
                $schedule[$match_num]['teambid'] = $teams[$match['teambid']];
                $match_num++;
            }
            
            $this->season_model->start_season($seasonid, $this->get_default_epoch($start_date), $this->get_default_epoch(date('Y-m-d',$this->schedule_maker->get_end_date())));
            $this->match_model->create_matches($leagueid, $schedule);
            $this->view($leagueid);
        }
    }


    public function view($leagueid) 
    {
        $this->require_login();
        $teams = $this->team_model->get_teams_byleagueid($leagueid,$_SESSION['esportid']);
        if(!$teams) 
        {
            $teams['teams'] = array();
        }
        $league = $this->league_model->get_league_details($leagueid);
        if($league['start_date'] != NULL)
        {
            //get the end date of the season
            $league['end_date'] = $this->get_local_date($league['end_date']);
            $league['start_date'] = $this->get_local_date($league['start_date']);
        }

        $schedule = array();
        if($league['season_status'] != 'new' && $league['start_date'] != NULL) 
        {
            $season['start_date'] = strtotime($league['start_date']);
            $season['end_date'] = strtotime($league['end_date']);
            $schedule = $this->match_model->get_matches_by_leagueid($leagueid, $season);
        }
        $data['teams'] = $teams;
        $data['league'] = $league;
        $data['schedule'] = $schedule;
        $this->view_wrapper('view_league', $data);

    }

    public function value_infuture($date)
    {
        if(strtotime('tomorrow') > strtotime($date))
        {
            $this->form_validation->set_message('value_infuture','Start date has to be in the future (Not today either)');
            return false;
        }
        else {
            return true;
        }
    }
}
