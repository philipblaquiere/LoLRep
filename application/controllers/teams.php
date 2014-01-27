<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends MY_Controller{
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
        $this->load->model('trade_lol_model');
        $this->load->model('team_invite_model');
        $this->load->model('season_model');
        $this->load->model('match_model');
        $this->load->model('riotapi_model');
        
    }
    public function index() {
        $this->require_login();
        $data['teams'] = $this->team_model->get_all_teams_by_uid($_SESSION['user']['UserId'],$_SESSION['esportid']);
        $data['invites'] = $this->team_invite_model->get_lol_new_invites_by_uid($_SESSION['user']['UserId']);
        if($data['invites']) {
            $this->team_invite_model->mark_invites_read($_SESSION['user']['UserId']);
        }
        $this->view_wrapper('user/teams', $data);
    }

    public function join_team() {
        $this->require_login();
    }

    public function invite($teamid) {
        $team = $this->team_model->get_team_by_teamid($teamid);
        if($team['esportid'] == 1) {
            $this->invite_lol($team);
        }
    }
    public function view($teamid) {
        $this->require_login();
        $this->load->library('calendar');
        
        $data['team'] = $this->team_model->get_team_by_teamid($teamid);
        $data['roster'] = $this->team_model->get_team_roster($teamid, $_SESSION['esportid']);
        $data['calendar'] = $this->calendar;
        $season = $this->season_model->get_current_season($teamid);
        $data['schedule'] = $this->match_model->get_matches_by_teamid($teamid,$season);
        //get the league;
        if($data['schedule']) {
            $leagueid = $data['schedule'][0]['leagueid'];
            $data['teams'] = $this->team_model->get_teams_byleagueid($leagueid,$_SESSION['esportid']);
        }
        print_r($data['roster']);
        $this->view_wrapper('view_team',$data);
    }

    public function invite_lol($team) {
        $this->require_login();
        $data['team'] = $team;

        $this->load->library('form_validation');

        $this->form_validation->set_rules('summonerlist', 'Summoners', 'trim|required|xss_clean|callback_summoner_registered|callback_summoner_inteam');
        $this->form_validation->set_rules('invite_message', 'Message', 'trim|required|xss_clean');

        if($this->form_validation->run() == FALSE){
            $this->view_wrapper('team_invite_lol',$data);
        }
        else {
            $invitations = $this->input->post();
            $invitation = array();
            $summonernames = explode(",", trim($invitations['summonerlist'],","));
            foreach ($summonernames as $summonername) {
                $invitation['summonerid'] = $this->lol_model->get_summonerid_from_summonername($summonername);
                $invitation['teamid'] = $team['teamid'];
                $invitation['message'] = $invitations['invite_message'];
                
                $this->team_invite_model->invite_summoner($invitation);
            }
            if(count($summonernames) == 1) {
                $this->system_message_model->set_message(join(', ', $summonernames)  . " has been invited to " . $team['team_name']  , MESSAGE_INFO);
            }
            else {
                 $this->system_message_model->set_message(join(', ', $summonernames)  . " have been invited to " . $team['team_name']  , MESSAGE_INFO);
            }
            redirect('home', 'refresh');
        }
    }

    public function summoner_registered($summonerlist) {
        //for callback
        //for summonernamestrim
        $summonerlist = trim($summonerlist,",");
        $summonernames = explode(",", $summonerlist);
        $callback = explode(",", $summonerlist);
        $invalidnames = array();
        foreach ($summonernames as $summonername) {
            if(!$this->lol_model->registered_summoner(trim($summonername))) {
               array_push($invalidnames,$summonername);
            }
        }
        if($invalidnames) {
            if(count($invalidnames) == 1) {
                $this->system_message_model->set_message(join(', ', $invalidnames)  . " is not registered in our systems."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_registered',  join(', ', $invalidnames)  . " is not registered in our systems");
            }
            else {
                $this->system_message_model->set_message(join(', ', $invalidnames)  . " are not registered in our systems."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_registered',  join(', ', $invalidnames)  . " are not registered in our systems");
            }
            return false;
        }
        else {
            return true;
        }
    }

    public function summoner_inteam($summonerlist) {
        //for callback, verifies summoner(s) are part of existing team
        $summonerlist = trim($summonerlist,",");
        $summonernames = explode(",", $summonerlist);
        $invalidnames = array();
        foreach ($summonernames as $summonername) {
            if($this->team_model->get_team_id_by_summonername(trim($summonername))) 
                array_push($invalidnames,$summonername);
        }
        if($invalidnames) {
            if(count($invalidnames) == 1) { 
                $this->system_message_model->set_message( join(', ', $invalidnames)  . " is already part of a team."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_inteam',  join(', ', $invalidnames)  . " is already part of a team.");
            }
            else {
                $this->system_message_model->set_message( join(', ', $invalidnames)  . " are already part of a team."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_inteam',  join(', ', $invalidnames)  . " are already part of a team.");
            }
            return false;
        }
        else {
            return true;
        }
    }
}