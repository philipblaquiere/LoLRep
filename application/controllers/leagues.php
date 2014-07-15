<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leagues extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
    private $MAX_LEAGUE_COUNT = 20;
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
        $this->load->model('match_model');
    }

    public function index()
    {
        $this->require_login();
        $leagues_info = $this->league_model->get_leagues($_SESSION['esportid']);
        $league_teams = $this->league_model->get_active_league_teams($_SESSION['esportid']);
        $captain_team = $this->team_model->get_team_by_captainid($this->get_userid(), $this->get_esportid());
        $user_current_teams = $this->team_model->get_teams_by_uid($this->get_userid(), $this->get_esportid());
        if(!empty($user_current_team))
        {
            $current_league = $this->league_model->get_leagues_by_teamids($user_current_teams);
        }
        else
        {
            //user isn't part of a team, let the user know that he can't join a league
            $this->system_message_model->set_message("You must be captain of your registered team to join a league!", MESSAGE_INFO);
        }
        
        foreach ($leagues_info as $league_info)
        {
            //Check if user can join league, can join if 
            /*  season is new
            *   isn't already part of the league
            *   league aint full   
            *   league aint invite only
            *   user is captain
            *   hasn't already changed league today
            */
            $leagues_info[$league_info['league_name']]['num_teams'] = array_key_exists($league_info['league_name'], $league_teams) ? count($league_teams[$league_info['league_name']]['teams']) : 0 ;

            if(empty($user_current_teams))
            {
                //User not part of a team
                $leagues_info[$league_info['league_name']]['can_join'] = FALSE;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "You need to be part of a registered team to join this league";
            }
            else if(!empty($current_league) && $current_league['leagueid'] == $leagues_info[$league_info['league_name']]['leagueid'])
            {
                $leagues_info[$league_info['league_name']]['can_join'] = FALSE;
                $leagues_info[$league_info['league_name']]['join_status'] = "Current";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "You are currently part of this league";
            }
            else if(!$captain_team)
            {
                //User is not a captain of any team
                $leagues_info[$league_info['league_name']]['can_join'] = FALSE;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "You must be captain of your team to join this league";
            }
            else if($league_info['invite'] == 1)
            {
                $leagues_info[$league_info['league_name']]['can_join'] = FALSE;
                $leagues_info[$league_info['league_name']]['join_status'] = "Invite Only";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "This league is invite only";
            }
            else if($leagues_info[$league_info['league_name']]['num_teams'] == $league_info['max_teams'])
            {
                $leagues_info[$league_info['league_name']]['can_join'] = FALSE;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "This league is full";
            }
            
            else
            {
                $leagues_info[$league_info['league_name']]['can_join'] = TRUE;
                $leagues_info[$league_info['league_name']]['join_status'] = "Join";
                $leagues_info[$league_info['league_name']]['join_status_tooltip'] = "Join this league";
            }
        }
        $data['league_teams'] = $league_teams;
        $data['leagues_info'] = $leagues_info;
        $data['current_league'] = empty($current_league) ? array() : $current_league;
        $data['max_league_count'] = $this->MAX_LEAGUE_COUNT;
        
        $this->view_wrapper('view_leagues', $data);
    }

    public function create()
    {
        $this->require_login();
        if(!$this->player_exists())
        {
            $this->system_message_model->set_message("Add an Esport to your account before creating a League. You must also be Captain of your team to create a league!"  , MESSAGE_ERROR);
            redirect('add_esport','refresh');
            return;
        }
        
        $data['league_types'] = $this->league_model->get_league_types();
        
        //Validation on input (requires that all fields exist)
        $this->load->library('form_validation');
        $this->form_validation->set_rules('typeid', 'Type', 'required');
        $this->form_validation->set_rules('name', 'League Name', 'trim|required|xss_clean|callback_unique_leaguename|callback_day_selected');
        $this->form_validation->set_rules('max_teams', 'Maximum # of Teams', 'trim|required|callback_valid_teamcount');

        if($this->form_validation->run() == FALSE)
        {
            $this->view_wrapper('create_league', $data);
        }

        else
        {
            $input = $this->input->post();
            $leagues_meta = array();
            $time = strtotime('now');

            //get times of day of week if corresponding checkbox is checked
            if(in_array("mondaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('monday',$input['mondaytimepicker'],$time));
            }
            if(in_array("tuesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('tuesday',$input['tuesdaytimepicker'],$time));
            }
            if(in_array("wednesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('wednesday',$input['wednesdaytimepicker'],$time));
            }
            if(in_array("thursdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('thursday',$input['thursdaytimepicker'],$time));
            }
            if(in_array("fridaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('friday',$input['fridaytimepicker'],$time));
            }
            if(in_array("saturdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('saturday',$input['saturdaytimepicker'],$time));
            }
            if(in_array("sundaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('sunday',$input['sundaytimepicker'],$time));
            }
            $season['userid'] = $this->get_userid();
            $season['season_duration'] = $input['duration'];
            $season['season_esportid'] = $this->get_esportid();

            $league['name'] = $input['name'];
            $league['esportid'] = $this->get_esportid();
            $league['max_teams'] = $input['max_teams'];
            $league['typeid'] = $input['typeid'];
            $league['invite'] = in_array("inviteonly", $input) ? 1 : 0;
            $league['privateleague'] = in_array("private", $input) ? 1 : 0;
            $league['league_meta'] = $leagues_meta;
            $this->view_wrapper('create_league', $data);
            if($this->league_model->create_league($league,$season))
            {
                $this->system_message_model->set_message('The League has been created.', MESSAGE_INFO);
            }
            redirect('home', 'refresh');
        }
    }

    private function _get_first_match_datetime($dayofweek,$timeofday,$seasonstartdate)
    {
        $firstmidnight = $this->_get_next_dayofweek($dayofweek, $seasonstartdate);
        $dt = new DateTime("@$firstmidnight");  // convert UNIX timestamp to PHP DateTime
        return $this->get_default_epoch(($dt->format('Y-m-d') . " " .$timeofday));
    }

    private function _get_next_dayofweek($day,$startdate)
    {
        return strtotime( "Next ". $day, $startdate);
    }

    public function day_selected()
    {
        $days = $this->input->post();
        if(in_array("mondaytimepicker", $days) || in_array("tuesdaytimepicker", $days) || in_array("wednesdaytimepicker", $days) || in_array("thursdaytimepicker", $days) || in_array("fridaytimepicker", $days) || in_array("saturdaytimepicker", $days) || in_array("sundaytimepicker", $days))
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('day_selected','You must select as least one day and time to play!');
            return FALSE;
        }
    }

    

    private function _owns_season($userid)
    {
        $user_seasons = $this->season_model->get_seasons_by_owner($this->get_userid(), $this->get_esportid());
        return $user_seasons;
    }

    public function unique_leaguename($new_league_name)
    {
        $existing_league = $this->league_model->get_league_by_name($new_league_name);

        if($existing_league && ($existing_league['status']=="new" || $existing_league['status']=="active"))
        {
            $this->form_validation->set_message('unique_leaguename','A league with an identical name already exists.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function valid_teamcount($teamcount)
    {
        if($teamcount < 6 || $teamcount > 32)
        {
            $this->form_validation->set_message('valid_teamcount','Maximum number of teams must be between (and including) 6 and 32.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}