<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends MY_Controller{
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
        $this->load->model('trade_lol_model');
        $this->load->model('invite_model');
        $this->load->model('season_model');
        $this->load->model('match_model');
        $this->load->model('riotapi_model');
        $this->load->model('league_model');

    }

    public function index()
    {
        $this->require_login();
        $data['teams'] = $this->team_model->get_teams_by_uid($this->get_userid(), $this->get_esportid());
        $data['invites'] = $this->invite_model->get_invites_by_uid($this->get_userid(), $this->get_esportid());
        if($data['invites'])
        {
            $this->invite_model->mark_invites_read($this->get_userid(), $this->get_esportid());
        }
        
        print_r($data);
        $this->view_wrapper('teams', $data);
    }

    public function create()
    {
        $this->require_login();
        $this->require_registered();
        print_r($this->get_player());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('teamname', 'Team Name', 'trim|required|xss_clean|callback_unique_teamname');

        if($this->form_validation->run() == FALSE)
        {
            $this->system_message_model->set_message(validation_errors()  , MESSAGE_ERROR);
            $this->view_wrapper('create_team');
        }
        else
        {
            $team['team_name'] = $this->input->post('teamname');
            $team['esportid'] = $this->get_esportid();
            //$team['captainid'] = $this->input->post('make_captain') ? $this->get_playerid() : null;
            $team['captainid'] = $this->get_playerid();
            $team['playerid'] = $this->get_playerid();
            $this->team_model->create_team($team);
            $this->system_message_model->set_message($team['team_name'] . ' has been created, add people to your team' , MESSAGE_INFO);
            redirect('home', 'location');
        }
    }

    public function join_team()
    {
        $this->require_login();
    }

    public function view($teamid)
    {
        $this->require_login();
        $this->load->library('calendar');

        $data['team'] = $this->team_model->get_team_by_teamid($teamid, $_SESSION['esportid']);
        //$data['team_details'] = $this->team_model->get_detailed_team_by_teamid($teamid,$_SESSION['esportid']);
        $data['roster'] = $this->team_model->get_team_roster($teamid, $_SESSION['esportid']);
        $data['calendar'] = $this->calendar;

        $data['schedule'] = array();
        $data['schedule'] = $this->match_model->get_matches_by_team($data['team']);
        //get the league;
        if($data['schedule'])
        {
            $league_details = $this->league_model->get_league_details($data['team']['leagueid']);
            $data['teams'] = $this->team_model->get_teams_byleagueid($data['team']['leagueid'],$_SESSION['esportid']);
        }
        $this->view_wrapper('view_team',$data);
    }

    public function invite_lol($team)
    {
        $this->require_login();
        $data['team'] = $team;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('summonerlist', 'Summoners', 'trim|required|xss_clean|callback_summoner_registered|callback_summoner_inteam');
        $this->form_validation->set_rules('invite_message', 'Message', 'trim|required|xss_clean');

        if($this->form_validation->run() == FALSE)
        {
            $this->view_wrapper('team_invite_lol',$data);
        }
        else
        {
            $invitations = $this->input->post();
            $invitation = array();
            $summoner_names = explode(",", trim($invitations['summonerlist'],","));
            foreach ($summoner_names as $summoner_name)
            {
                $invitation['summonerid'] = $this->lol_model->get_summonerid_from_summonername($summoner_name);
                $invitation['teamid'] = $team['teamid'];
                $invitation['message'] = $invitations['invite_message'];

                $this->team_invite_model->invite_summoner($invitation);
            }
            if(count($summoner_names) == 1)
            {
                $this->system_message_model->set_message(join(', ', $summoner_names)  . " has been invited to " . $team['team_name']  , MESSAGE_INFO);
            }
            else
            {
                $this->system_message_model->set_message(join(', ', $summoner_names)  . " have been invited to " . $team['team_name']  , MESSAGE_INFO);
            }
            redirect('home', 'refresh');
        }
    }


    public function summoner_registered($summonerlist)
    {
        //for callback
        //for summoner_namestrim
        $summonerlist = trim($summonerlist,",");
        $summoner_names = explode(",", $summonerlist);
        $callback = explode (",", $summonerlist);
        $invalidnames = array();
        foreach ($summoner_names as $summoner_name)
        {
            if(!$this->lol_model->registered_summoner(trim($summoner_name)))
            {
               array_push($invalidnames,$summoner_name);
            }
        }
        if($invalidnames)
        {
            if(count($invalidnames) == 1)
            {
                $this->system_message_model->set_message(join(', ', $invalidnames)  . " is not registered in our systems."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_registered',  join(', ', $invalidnames)  . " is not registered in our systems");
            }
            else
            {
                $this->system_message_model->set_message(join(', ', $invalidnames)  . " are not registered in our systems."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_registered',  join(', ', $invalidnames)  . " are not registered in our systems");
            }
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function summoner_inteam($summonerlist)
    {
        //for callback, verifies summoner(s) are part of existing team
        $summonerlist = trim($summonerlist,",");
        $summoner_names = explode(",", $summonerlist);
        $invalidnames = array();
        foreach ($summoner_names as $summoner_name)
        {
            if($this->team_model->get_team_id_by_summonername(trim($summoner_name)))
                array_push($invalidnames,$summoner_name);
        }
        if($invalidnames) {
            if(count($invalidnames) == 1)
            {
                $this->system_message_model->set_message( join(', ', $invalidnames)  . " is already part of a team."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_inteam',  join(', ', $invalidnames)  . " is already part of a team.");
            }
            else
            {
                $this->system_message_model->set_message( join(', ', $invalidnames)  . " are already part of a team."  , MESSAGE_ERROR);
                $this->form_validation->set_message('summoner_inteam',  join(', ', $invalidnames)  . " are already part of a team.");
            }
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function unique_teamname($teamname)
    {
        $existing_team = $this->team_model->get_team_by_name($teamname, $this->get_esportid());
        if($existing_team)
        {
            $this->form_validation->set_message('unique_teamname','A team with an identical name already exists.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}
