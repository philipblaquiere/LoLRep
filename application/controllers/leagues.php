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
        $leagues = $this->league_model->get_leagues($this->get_esportid());
        $leagueids = $this->_extract_values('leagueid', $leagues);
        $league_teams = $this->league_model->get_league_teams($this->get_esportid(),$leagueids);
        $player = $this->get_player();
        $captain_team = $this->team_model->get_team_by_captainid($player['playerid'], $this->get_esportid());
        $player_teams = $this->team_model->get_teams_by_playerid($player['playerid'], $this->get_esportid());
        print_r($leagues);
        if(empty($player_teams))
        {
           //user isn't part of a team, let the user know that he can't join a league
            $this->system_message_model->set_message("You must be part and captain of a team to join a league.", MESSAGE_WARNING);
        }
        
        foreach ($leagues as $league)
        {
            //Check if user can join league, can join if 
            /*  season is new
            *   isn't already part of the league
            *   league aint full   
            *   league aint invite only
            *   user is captain
            *   hasn't already changed league today
            */
            $leagues[$league['leagueid']]['num_teams'] = array_key_exists($league['leagueid'], $league_teams) ? count($league_teams[$league['leagueid']]['teams']) : 0 ;

            if(empty($player_teams))
            {
                //User not part of a team
                $leagues[$league['leagueid']]['can_join'] = FALSE;
                $leagues[$league['leagueid']]['join_status'] = "Join";
                $leagues[$league['leagueid']]['join_status_tooltip'] = "You need to be part of a registered team to join this league";
            }
            else if(!empty($current_league) && $current_league['leagueid'] == $leagues[$league['leagueid']]['leagueid'])
            {
                $leagues[$league['leagueid']]['can_join'] = FALSE;
                $leagues[$league['leagueid']]['join_status'] = "Current";
                $leagues[$league['leagueid']]['join_status_tooltip'] = "You are currently part of this league";
            }
            else if(!$captain_team)
            {
                //User is not a captain of any team
                $leagues[$league['leagueid']]['can_join'] = FALSE;
                $leagues[$league['leagueid']]['join_status'] = "Join";
                $leagues[$league['leagueid']]['join_status_tooltip'] = "You must be captain of your team to join this league";
            }
            else if($league['invite'] == 1)
            {
                $leagues[$league['leagueid']]['can_join'] = FALSE;
                $leagues[$league['leagueid']]['join_status'] = "Invite Only";
                $leagues[$league['leagueid']]['join_status_tooltip'] = "This league is invite only";
            }
            else if($leagues[$league['leagueid']]['num_teams'] == $league['max_teams'])
            {
                $leagues[$league['leagueid']]['can_join'] = FALSE;
                $leagues[$league['leagueid']]['join_status'] = "Full";
                $leagues[$league['leagueid']]['join_status_tooltip'] = "This league is full";
            }
            
            else
            {
                $leagues[$league['leagueid']]['can_join'] = TRUE;
                $leagues[$league['leagueid']]['join_status'] = "Join";
                $leagues[$league['leagueid']]['join_status_tooltip'] = "Join this league";
            }
        }
        $data['captain_team'] = $captain_team;
        $data['league_teams'] = $league_teams;
        $data['leagues_info'] = $leagues;
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

    public function join($leagueid)
    {
        $player = $this->get_player();
        $captain_team = $this->team_model->get_team_by_captainid($player['playerid'], $this->get_esportid());
        $team = $this->team_model->get_team_by_teamid($captain_team['teamid'], $this->get_esportid());

        //$current_league = $this->league_model->get_current_league_by_teamid($team['teamid']);

        if($team['league'])
        {
            //Team is already part of league, check if team joined the league sometime today.
            $today_midnight = $this->get_default_epoch($this->get_local_datetime(strtotime('today midnight')));
            $time_now = strtotime($this->get_local_datetime(time()));
            $joined_date = strtotime($this->get_local_datetime(strtotime($current_league['joined'])));
            if($time_now - $joined_date < $this->UNIX_DAY)
            {
                $time_remaining = $this->UNIX_DAY - ($time_now - $joined_date);
                //joined today, ask to wait.
                $this->system_message_model->set_message("You recently joined another league, you must wait 24h to switch league. You have until the registration period ends.", MESSAGE_INFO);
                redirect('leagues', 'refresh');
            }
            else
            {
                //Remove from current league and join the other league
                $this->league_model->leave_league($captain_team['teamid'], $team['league']['leagueid']);
                $joined_league = $this->league_model->join_league($captain_team['teamid'],$leagueid);
                $this->system_message_model->set_message("You have successfully joined the league" , MESSAGE_INFO);
                redirect('leagues', 'refresh');
            }
        }
        else
        {
            //Join the league : Player is not part of team and is captain of his team, 
            $joined_league = $this->league_model->join_league($leagueid, $captain_team['teamid']);
            $this->system_message_model->set_message("You have successfully joined the league" , MESSAGE_INFO);
            redirect('leagues', 'refresh');
        }
    }

    public function start_season($seasonid)
    {
        $this->require_login();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('season_start_date', 'Season Start', 'required|callback_date_infuture');

        $leagueid = $this->input->post('leagueid');

        if($this->form_validation->run() == FALSE)
        {
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
            foreach ($league_teams['teams'] as $team)
            {
                array_push($teams, $team['teamid']);
            }
            $match_num = 0;
            foreach ($schedule as $match) 
            {
                $schedule[$match_num]['teamaid'] = $teams[$match['teamaid']];
                $schedule[$match_num]['teambid'] = $teams[$match['teambid']];
                $match_num++;
            }
            
            $this->season_model->start_season($seasonid, $this->get_default_epoch($start_date), $this->get_default_epoch(date('Y-m-d',$this->schedule_maker->get_end_date())));
            $this->match_model->create_matches($leagueid, $schedule);
            $this->view($leagueid);
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

    private function _extract_values($value, $array)
    {
        $result = array();
        foreach ($array as $array_item)
        {
            array_push($result, $array_item[$value]);
        }
        return $result;
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