<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller{
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
        $this->load->model('riotapi_model');
        $this->load->model('team_invite_model');
        $this->load->model('banned_model');
        $this->load->model('season_model');
        $this->load->model('league_model');
        $this->load->model('match_model');
    }

    public function index() {
        $data['result'] = array();
        $data['new_seasons'] = $this->season_model->get_new_season();
        $this->view_wrapper('admin_panel',$data);
    }

    public function update_lol_champions() {
        $champions = $this->riotapi_model->getChampions();
        $this->lol_model->update_lol_champions($champions);
        $this->system_message_model->set_message("Update League of Legends Champions Complete" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');
    }

    public function ban_summoner_byemail() {
        $this->load->library('form_validation');
        $email = $this->input->post('ban_email');
        $reason = $this->input->post('ban_reason');
        $user = $this->banned_model->get_summoner_byemail($email);
        $this->banned_model->_ban_summoner($user,$reason);
        $this->system_message_model->set_message("User has been banned" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');
    }

    public function ban_summoner_by_summonername() {
        $this->load->library('form_validation');
        $summonername = $this->input->post('ban_summonername');
        $reason = $this->input->post('ban_reason');
        $user = $this->banned_model->get_summoner_by_summonername($summonername);
        $this->banned_model->_ban_summoner($user,$reason);
        $this->system_message_model->set_message("User has been banned" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');
    }

    private function _ban_summoner($user,$reason) {
        $this->banned_model->ban_summoner($user,$reason);
    }

    public function create_season() {
        $name = $this->input->post('name');
        $registration_start = $this->input->post('registration_start');
        $enddate = $this->input->post('enddate');
        
        $season['registration_start'] = $this->get_default_epoch($registration_start);
        $registration_start = date ("Y-m-d H:i:s", strtotime($registration_start));
        $registration_end = new DateTime($registration_start);
        $registration_end->modify('+7 day');
        $registration_end->modify('-1 second');
        $startdate = new DateTime($registration_start);
        $startdate->modify('+1 week');
        $season['registration_end'] = $this->get_default_epoch($registration_end->format('Y-m-d H:i:s'));
        $season['startdate'] = $this->get_default_epoch($startdate->format('Y-m-d H:i:s'));
        $modified_end = new DateTime($enddate);
        $modified_end->modify('+1 day');
        $modified_end->modify('-1 second');
        $season['enddate'] = $this->get_default_epoch($modified_end->format('Y-m-d H:i:s'));
        $season['name'] = $name;
        $season['UserId'] = $_SESSION['user']['UserId'];
        $this->season_model->create_season($season);
        $this->system_message_model->set_message("Season " . $season['name'] . " has been created!" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');
    }

    public function open_season() {
        $season = $this->input->post();
        print_r($season);
        /*$this->season_model->open_season($season);
        $this->system_message_model->set_message("Season " . $season['name'] . " has been opened!" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');*/
    }

    public function create_matches_for_season() {

        $leagueid = $this->input->post('leagueid');
        $season = $this->season_model->get_new_season();
        $teams_details = $this->team_model->get_teams_byleagueid($leagueid,$_SESSION['esportid']);
        $first_matches = $this->league_model->get_active_league_first_matches($leagueid);

        $this->load->library('schedule_maker');
        $this->schedule_maker->set_num_teams(count($teams_details['teams']));
        $this->schedule_maker->set_start_end_dates($season['startdate'],$season['enddate']);
        $this->schedule_maker->set_matches($first_matches);
        $generated_schedule = $this->schedule_maker->generate_schedule();

        $teams = array();
        $schedule = array();
        foreach ($teams_details['teams'] as $team) {
            array_push($teams, $team['teamid']);
        }
        foreach ($generated_schedule as $match) {
            $match_details = array();
            $match_details['teamaid'] = $teams[$match['teamaid']];
            $match_details['teambid'] = $teams[$match['teambid']];
            $match_details['match_date'] = $match['match_date'];
            array_push($schedule, $match_details);
        }
        $this->match_model->create_matches($leagueid, $schedule);
    }
}