<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_league extends MY_Controller{
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
        $season = $this->season_model->get_new_season();
        $esports = $this->esport_model->get_all_registered_esports($_SESSION['user']['UserId']);

        if(!$esports) {
            $data['esports'] = $this->esport_model->get_all_esports();
            $this->system_message_model->set_message("Add an Esport to your account before creating a League. You must also be Captain of your team to create a league!"  , MESSAGE_ERROR);
            $this->view_wrapper('user/add_esport',$data);
            return;
        }
        $data['league_types'] = $this->league_model->get_league_types();
        $data['season'] = $season;
        $data['season']['registration_start'] = $this->get_local_date($data['season']['registration_start']);
        $data['season']['registration_end'] = $this->get_local_datetime($data['season']['registration_end']);
        $data['season']['startdate'] = $this->get_local_date($data['season']['startdate']);
        $data['season']['enddate'] = $this->get_local_datetime($data['season']['enddate']);
        $data['esports'] = $esports;
        
        //Validation on input (requires that all fields exist)
        $this->load->library('form_validation');
        $this->form_validation->set_rules('typeid', 'Type', 'required');
        $this->form_validation->set_rules('esportid', 'ESport', 'trim');
        $this->form_validation->set_rules('name', 'League Name', 'trim|required|xss_clean|callback_unique_leaguename');
        $this->form_validation->set_rules('max_teams', 'Maximum # of Teams', 'trim|required|callback_valid_teamcount');
        $this->form_validation->set_rules('inviteonlyleaguecheckbox', 'Invite Only');
        $this->form_validation->set_rules('privateleaguecheckbox', 'Private League');
        $this->form_validation->set_rules('privateleaguecheckbox', 'Private League');
        $this->form_validation->set_rules('mondaycheckbox', 'Games Monday');
        $this->form_validation->set_rules('tuesdaycheckbox', 'Games Tuesday');
        $this->form_validation->set_rules('wednesdaycheckbox', 'Games Wednesday');
        $this->form_validation->set_rules('thursdaycheckbox', 'Games Thursday');
        $this->form_validation->set_rules('fridaycheckbox', 'Games Friday');
        $this->form_validation->set_rules('saturdaycheckbox', 'Games Saturday');
        $this->form_validation->set_rules('sundaycheckbox', 'Games Sunday');
        $this->form_validation->set_rules('mondaytimepicker', 'Game Time Monday');
        $this->form_validation->set_rules('tuesdaytimepicker', 'Game Time Tuesday');
        $this->form_validation->set_rules('wednesdaytimepicker', 'Game Time Wednesday');
        $this->form_validation->set_rules('thursdaytimepicker', 'Game Time Thursday');
        $this->form_validation->set_rules('fridaytimepicker', 'Game Time Friday');
        $this->form_validation->set_rules('saturdatimepicker', 'Game Time Saturday');
        $this->form_validation->set_rules('sundaytimepicker', 'Game Time Sunday');

        if($this->form_validation->run() == FALSE) {
            $this->view_wrapper('create_league', $data);
        }

        else {
            $input = $this->input->post();
            $leagues_meta = array();
            if(in_array("mondaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('monday',$input['mondaytimepicker'],$season['startdate']));
            }
            if(in_array("tuesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('tuesday',$input['tuesdaytimepicker'],$season['startdate']));
            }
            if(in_array("wednesdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('wednesday',$input['wednesdaytimepicker'],$season['startdate']));
            }
            if(in_array("thursdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('thursday',$input['thursdaytimepicker'],$season['startdate']));
            }
            if(in_array("fridaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('friday',$input['fridaytimepicker'],$season['startdate']));
            }
            if(in_array("saturdaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('saturday',$input['saturdaytimepicker'],$season['startdate']));
            }
            if(in_array("sundaytimepicker", $input)) {
                array_push($leagues_meta, $this->get_first_game_datetime('sunday',$input['sundaytimepicker'],$season['startdate']));
            }
            $league['name'] = $input['name'];
            $league['esportid'] = $input['esportid'];
            $league['max_teams'] = $input['max_teams'];
            $league['typeid'] = $input['typeid'];
            $league['invite'] = in_array("inviteonly", $input) ? 1 : 0;
            $league['privateleague'] = in_array("private", $input) ? 1 : 0;
            $league['leagues_meta'] = $leagues_meta;
            $league['seasonid'] = $season['seasonid'];
            $this->view_wrapper('create_league', $data);

            if($this->league_model->create_league($league))
                $this->system_message_model->set_message('The League has been created.', MESSAGE_INFO);
            redirect('home', 'refresh');
        }
    }
    public function get_first_game_datetime($dayofweek,$timeofday,$seasonstartdate) {
        $firstmidnight = $this->get_next_dayofweek($dayofweek, $seasonstartdate);
        $dt = new DateTime("@$firstmidnight");  // convert UNIX timestamp to PHP DateTime
        return $this->get_default_epoch(($dt->format('Y-m-d') . " " .$timeofday));
    }

    public function get_next_dayofweek($day,$startdate) {
        return strtotime( "Next ". $day, $startdate);
    }

    public function valid_teamcount($teamcount) {
        if($teamcount < 6 || $teamcount > 32) {
            $this->form_validation->set_message('valid_teamcount','Maximum number of teams must be between (and including) 6 and 32.');
            return false;
        }
        else {
            return true;
        }
    }
    public function unique_leaguename($leaguename)
    {
        $openseason = $this->season_model->get_new_season();
        $existing_league = $this->league_model->get_league_by_name($leaguename);
        if($existing_league) {
            $this->form_validation->set_message('unique_leaguename','A league with an identical name already exists.');
            return false;
        }
        else {
            return true;
        }
    }
}