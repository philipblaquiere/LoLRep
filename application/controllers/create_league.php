<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_league extends MY_Controller{
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
        $this->load->model('league_model');
        $this->load->model('season_model');
    }

    public function index()
    {
        $this->require_login();
        $registered_esports = $this->esport_model->get_all_registered_esports($_SESSION['user']['UserId']);
        if(!$registered_esports)
        {
            $data['esports'] = $this->esport_model->get_all_esports();
            $this->system_message_model->set_message("Add an Esport to your account before creating a League. You must also be Captain of your team to create a league!"  , MESSAGE_ERROR);
            $this->view_wrapper('user/add_esport',$data);
            return;
        }
        
        $data['league_types'] = $this->league_model->get_league_types();
        $data['esports'] = $registered_esports;
        
        //Validation on input (requires that all fields exist)
        $this->load->library('form_validation');
        $this->form_validation->set_rules('typeid', 'Type', 'required');
        $this->form_validation->set_rules('esportid', 'ESport', 'trim');
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
                array_push($leagues_meta, $this->get_first_match_datetime('monday',$input['mondaytimepicker'],$time));
            }
            if(in_array("tuesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_match_datetime('tuesday',$input['tuesdaytimepicker'],$time));
            }
            if(in_array("wednesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_match_datetime('wednesday',$input['wednesdaytimepicker'],$time));
            }
            if(in_array("thursdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_match_datetime('thursday',$input['thursdaytimepicker'],$time));
            }
            if(in_array("fridaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_match_datetime('friday',$input['fridaytimepicker'],$time));
            }
            if(in_array("saturdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_match_datetime('saturday',$input['saturdaytimepicker'],$time));
            }
            if(in_array("sundaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_match_datetime('sunday',$input['sundaytimepicker'],$time));
            }
            $season['UserId'] = $_SESSION['user']['UserId'];
            $season['season_duration'] = $input['duration'];
            $season['season_esportid'] = $input['esportid'];

            $league['name'] = $input['name'];
            $league['esportid'] = $input['esportid'];
            $league['max_teams'] = $input['max_teams'];
            $league['typeid'] = $input['typeid'];
            $league['invite'] = in_array("inviteonly", $input) ? 1 : 0;
            $league['privateleague'] = in_array("private", $input) ? 1 : 0;
            $league['leagues_meta'] = $leagues_meta;
            $this->view_wrapper('create_league', $data);
            if($this->league_model->create_league($league,$season))
            {
                $this->system_message_model->set_message('The League has been created.', MESSAGE_INFO);
            }
            redirect('home', 'refresh');
        }
    }

    private function get_first_match_datetime($dayofweek,$timeofday,$seasonstartdate)
    {
        $firstmidnight = $this->get_next_dayofweek($dayofweek, $seasonstartdate);
        $dt = new DateTime("@$firstmidnight");  // convert UNIX timestamp to PHP DateTime
        return $this->get_default_epoch(($dt->format('Y-m-d') . " " .$timeofday));
    }

    private function get_next_dayofweek($day,$startdate)
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
    public function unique_leaguename($leaguename)
    {
        $existing_league = $this->league_model->get_league_by_name($leaguename);
        $user_own_season = $this->season_model->get_user_created_seasons($_SESSION['user']['UserId']);

        if($user_own_season)
        {
            $this->system_message_model->set_message("You already own a League! You can join other leagues but can only be owner of a single league."  , MESSAGE_ERROR);
            $this->view_wrapper('home',$data);
            return;
        }

        if($existing_league && ($existing_league['status']=="new" || $existing_league['status']=="active"))
        {
            $this->form_validation->set_message('unique_leaguename','A league with an identical name already exists.');
            return FALSE;
        }
        else if($user_own_season)
        {
            //user already created a league this season, check if league is for the current esport
            $esportid = $this->input->post('esportid');
            if($league['esportid'] == $user_own_season['season_esportid'])
            {
                $this->form_validation->set_message('unique_leaguename','You can only create one League per registered Esport.');
                return FALSE;
            }
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }
}