<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leagues extends MY_Controller
{
    const DESCRIPTION_MAX_CHARACTERS = 500;

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
        $this->load->model('player_model');
        $this->load->model('team_model');
        $this->load->library('league_standings');
    }

    public function index()
    {
        $leagues = $this->league_model->get_all_leagues($this->get_esportid());
        $player_teams = array();
        $captain_team = array();
        $player = array();
        if($this->player_exists())
        {
            $player = $this->get_player();
            $captain_team = $this->team_model->get_team_by_captainid($player['playerid'], $this->get_esportid());
            $player_teams = $this->team_model->get_teams_by_playerid($player['playerid'], $this->get_esportid());
        }

        $data['captain_team'] = $captain_team;
        $data['leagues'] = $leagues;
        $data['current_league'] = empty($current_league) ? array() : $current_league;
        
        $this->view_wrapper('view_leagues', $data, false);
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
        $this->form_validation->set_rules('league_name', 'League Name', 'trim|required|xss_clean|callback_unique_leaguename|callback_day_selected');
        $this->form_validation->set_rules('max_teams', 'Maximum # of Teams', 'callback_valid_teamcount');
        $this->form_validation->set_rules('league_description', 'League Description', 'trim|xss_clean|callback_character_count');

        if($this->form_validation->run() == FALSE)
        {
            $this->view_wrapper('create_league', $data, false);
        }

        else
        {
            $input = $this->input->post();
            $leagues_meta = array();
            $time = strtotime('now');

            //get times of day of week if corresponding checkbox is checked
            if(in_array("mondaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('monday',$input['mondaytimepicker'], $time));
            }
            if(in_array("tuesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('tuesday',$input['tuesdaytimepicker'], $time));
            }
            if(in_array("wednesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('wednesday',$input['wednesdaytimepicker'], $time));
            }
            if(in_array("thursdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('thursday',$input['thursdaytimepicker'], $time));
            }
            if(in_array("fridaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('friday',$input['fridaytimepicker'], $time));
            }
            if(in_array("saturdaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('saturday',$input['saturdaytimepicker'], $time));
            }
            if(in_array("sundaytimepicker", $input)) {
                array_push($leagues_meta, $this->_get_first_match_datetime('sunday',$input['sundaytimepicker'], $time));
            }
            $season['userid'] = $this->get_userid();
            $season['season_duration'] = $input['duration'];
            $season['season_esportid'] = $this->get_esportid();

            $league['name'] = $input['league_name'];
            $league['esportid'] = $this->get_esportid();
            $league['max_teams'] = $input['max_teams'];
            $league['typeid'] = $input['typeid'];
            $league['invite'] = in_array("invite", $input) ? 1 : 0;
            $league['private'] = in_array("private", $input) ? 1 : 0;
            $league['league_meta'] = $leagues_meta;
            $league['description'] = $input['league_description'];
            $this->view_wrapper('create_league', $data, false);
            if($this->league_model->create_league($league,$season))
            {
                $this->system_message_model->set_message('The League has been created.', MESSAGE_INFO);
            }
            redirect('leagues', 'refresh');
        }
    }

    public function join($leagueid)
    {
        $player = $this->get_player();
        $captain_team = $this->team_model->get_team_by_captainid($player['playerid'], $this->get_esportid());
        $team = $this->team_model->get_team_by_teamid($captain_team['teamid'], $this->get_esportid());
        $league = $this->league_model->get_league($leagueid);

        if($team['league'])
        {
            //Remove from current league and join the other league
            $this->league_model->leave_league($captain_team['teamid'], $team['league']['leagueid'], $league['current_season']);
            $joined_league = $this->league_model->join_league($leagueid, $captain_team['teamid']);
            $this->system_message_model->set_message("You have successfully joined the league" , MESSAGE_INFO);
            redirect('leagues', 'refresh');
        }
        else
        {
            //Join the league : Player is not part of team and is captain of his team, 
            $joined_league = $this->league_model->join_league($captain_team['teamid'], $leagueid, $league['current_season'] );
            $this->system_message_model->set_message("You have successfully joined the league" , MESSAGE_INFO);
            redirect('leagues', 'refresh');
        }
    }

    public function view($leagueid) 
    {
        $league = $this->league_model->get_leagues(array($leagueid));
        $league = $league[$leagueid];
        $league_teams = isset($league['seasons'][$league['current_season']]['teams']) ? $league['seasons'][$league['current_season']]['teams'] : array();
        $player = $this->get_player();
        if(!$this->_can_player_view_league($player, $league))
        {
            redirect('leagues', 'refresh');
        }
        $join_button = $this->_can_player_join_league($player, $league);
        $season = array();

        foreach ($league['seasons'] as $league_season)
        {
            if($league_season['season_status'] == 'new' || $league_season['season_status'] == 'active')
            {
                $season = $league_season;
                break;
            }
        }
        if($season['start_date'] != NULL)
        {
            //get the end date of the season
            $season['end_date'] = $this->gmt_to_local($season['end_date']);
            $season['start_date'] = $this->gmt_to_local($season['start_date']);
        }

        $schedule = array();
        if($season['season_status'] != 'new' && $season['start_date'] != NULL) 
        {
            $schedule = $this->match_model->get_matches_by_leagueid($leagueid, $season);
            foreach ($schedule as &$match)
            {
                    $match['match_date'] = $this->gmt_to_local($match['match_date']);
            }
        }

        if(!empty($schedule))
        {
            $standings = $this->league_standings->get_standings($league, $schedule);
            $data['standings'] = $standings;
        }
        $data['player'] = $player;
        $data['join_button'] = $join_button;
        $data['season'] = $season;
        $data['teams'] = $league_teams;
        $data['league'] = $league;
        $data['schedule'] = $schedule;
        $this->view_wrapper('view_league', $data, false);
    }

    public function start_season($seasonid)
    {
        $this->require_login();
        $this->load->library('form_validation');
        $userid = $this->get_userid();

        if(empty($userid))
        {
            redirect('home', 'refresh');
        }

        $this->form_validation->set_rules('season_start_date', 'Season Start', 'required|callback_date_infuture');

        $leagueid = $this->input->post('leagueid');

        if($this->form_validation->run() == FALSE)
        {
            $this->system_message_model->set_message(validation_errors()  , MESSAGE_ERROR);
            $this->view($leagueid);
        }
        else
        {
            $league = $this->league_model->get_league($leagueid);
            $league = $league[$leagueid];
            //User cant start the season if not league owner and if current season is active.
            if($userid != $league['ownerid'] || $league['seasons'][$league['current_season']]['status'] == 'active')
            {
                redirect('home', 'refresh');
            }
            $league_teams = $this->league_model->get_league_teams($this->get_esportid(),array($leagueid));
            $league_teams = $league_teams[$leagueid];
            $start_date = $this->input->post('season_start_date');

            $this->load->library('schedule_maker');
            $this->schedule_maker->set_num_teams(count($league_teams['teams']));

            $this->schedule_maker->set_start_date($start_date);
            $this->schedule_maker->set_duration($league['seasons'][$seasonid]['season_duration']);
            $this->schedule_maker->set_matches($league['seasons'][$seasonid]['first_matches']);
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

             $this->season_model->start_season($seasonid, $this->get_default_epoch($start_date), $this->get_default_epoch(date('Y-m-d',$this->schedule_maker->get_end_date())), $teams);
            $this->match_model->create_matches($leagueid, $seasonid, $schedule);
            redirect('leagues/view/'.$leagueid,'refresh');
        }
    }

    private function _can_player_view_league($player, $league)
    {
        if($league['private'] == 1)
        {
            //Check to see if players team is part of League.
            if(!empty($player) && count($player['teams'] > 0))
            {
                //Player is in a team, check if teamid is in league teams
                $player_teamid = $player['teams']['teamid'];
                if(isset($league['seasons']) && isset($league['seasons'][$league['current_season']]['teams']) && array_key_exists($player_teamid, $league['seasons'][$league['current_season']]['teams']))
                {
                    //Team is in private league
                    return TRUE;
                }
            }
            return FALSE;
        }
        return TRUE;
    }

    private function _can_player_join_league($player, $league)
    {
        $response = array();
        if($league['invite'] == 1)
        {
            //League is invite only
            $response['display_button'] = FALSE;
            $response['label'] = "THIS LEAGUE IS INVITE ONLY";
            return $response;
        }
        elseif(empty($player))
        {
            $response['url'] = "add_esport";
            $response['display_button'] = TRUE;
            $response['label'] = "REGISTER TO JOIN THIS LEAGUE";
            return $response;
        }
        elseif(count($player['teams']) == 0)
        {
            $response['url'] = "teams/create";
            $response['display_button'] = TRUE;
            $response['label'] = "CREATE/JOIN A TEAM TO JOIN THIS LEAGUE";
            return $response;
        }
        
        else
        {
            $team_details = $this->team_model->get_team($player['teams']['teamid']);
            if($team_details['captainid'] != $player['playerid'])
            {
                //Player isn't team's captain
                foreach ($team_details['players'] as $player)
                {
                    if($team_details['captainid'] == $player['playerid'])
                    {
                        $team_captain_name = $player['player_name'];
                        continue;
                    }
                }
                $response['url'] = "#";
                $response['display_button'] = TRUE;
                $response['label'] = "ASK " . $team_captain_nam . " TO JOIN THIS LEAGUE";
                return $response;
            }
            elseif (count($team_details['players']) < $this->get_min_players($this->get_esportid()))
            {
                //Team isn't complete, redirect to market.
                $response['url'] = "market";
                $response['display_button'] = TRUE;
                $response['label'] = "ADD PLAYERS TO YOUR TEAM";
                return $response;
            }
            elseif (isset($team_details['leagues']['current_league']))
            {
                $current_season = $team_details['leagues']['current_season'];
                $current_league = $team_details['leagues']['current_league'];
                if($current_league == $league['leagueid'])
                {
                    //Player is already part of this league
                    $response['display_button'] = FALSE;
                    return $response;
                }
                elseif($team_details['leagues'][$current_league]['seasons'][$current_season]['start_date'] == "")
                {
                    $response['url'] = "leagues/join/".$league['leagueid'];
                    $response['display_button'] = TRUE;
                    $response['label'] = "LEAVE ". $team_details['leagues'][$current_league]['league_name'] ." AND JOIN THIS LEAGUE";
                    return $response;
                }
                //LOGIC FOR MULTIPLE LEAGUES START HERE, SINGLE LEAGUE FOR NOW
                else
                {
                    //Player can leave their league, since it already started
                    $response['url'] = "#";
                    $response['display_button'] = FALSE;
                    $response['label'] = "ALREADY PART OF AN ACTIVE LEAGUE";
                    return $response;
                }
            }
            else
            {
                 
                //Player is captain, has a complete team, and isn't part of a league
                $response['url'] = "leagues/join/".$league['leagueid'];
                $response['display_button'] = TRUE;
                $response['label'] = "JOIN THIS LEAGUE";
                return $response;
            }
        }
        return $response;
    }

    private function _get_first_match_datetime($dayofweek, $timeofday, $seasonstartdate)
    {
        $firstmidnight = $this->_get_next_dayofweek($dayofweek, $seasonstartdate);
        $dt = new DateTime("@$firstmidnight");  // convert UNIX timestamp to PHP DateTime
        return $this->local_to_gmt(human_to_unix(($dt->format('Y-m-d') . " " .$timeofday)), FALSE);
    }

    private function _get_next_dayofweek($day,$startdate)
    {
        return strtotime( "Next ". $day, $startdate);
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

    //=================
    //  CALLBACKS
    // ================   

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

    public function character_count($description)
    {
        if(strlen($description) > self::DESCRIPTION_MAX_CHARACTERS)
        {
            $this->form_validation->set_message('character_count','League description must have 500 or less characters');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
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
    //=================
    //  END CALLBACKS
    // ================   

}