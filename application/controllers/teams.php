<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
    {
        parent::__construct();
        $this->load->model('system_message_model');
        $this->load->model('ip_log_model');
        $this->load->model('esport_model');
        $this->load->model('team_model');
        $this->load->model('lol_model');
        $this->load->model('match_model');
        $this->load->model('league_model');
    }

 
    public function view($teamid)
    {
        $team = $this->team_model->get_teams(array($teamid), $this->get_esportid());
        $team = $team[$teamid];
        $data['team'] = $team;
        $data['banner']['title_big'] = $team['team_name'];
        //Verify if season started
        
        $this->view_wrapper('view_team',$data);
    }

    public function create()
    {
        $this->require_login();
        $this->require_registered();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('teamname', 'Team Name', 'trim|required|xss_clean|callback_unique_teamname');
        if($this->form_validation->run() == FALSE)
        {
            $this->view_wrapper('create_team', array(), false);
        }
        else
        {
            $player = $this->get_player();
            $team['team_name'] = $this->input->post('teamname');
            $team['esportid'] = $this->get_esportid();
            //$team['captainid'] = $this->input->post('make_captain') ? $this->get_playerid() : null;
            $team['captainid'] = $player['playerid'];
            $team['playerid'] = $player['playerid'];
            $this->team_model->create_team($team);
            $this->system_message_model->set_message($team['team_name'] . ' has been created, add people to your team' , MESSAGE_INFO);
            redirect('home', 'location');
        }
    }

    public function join_team()
    {
        $this->require_login();
    }

    public function invite_lol($team)
    {
        $this->require_login();
        $data['team'] = $team;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('player_list', 'Summoners', 'trim|required|xss_clean|callback_player_registered|callback_player_inteam');
        $this->form_validation->set_rules('invite_message', 'Message', 'trim|required|xss_clean');

        if($this->form_validation->run() == FALSE)
        {
            $this->view_wrapper('team_invite_lol',$data, false);
        }
        else
        {
            $invitations = $this->input->post();
            $invitation = array();
            $player_names = explode(",", trim($invitations['player_list'],","));
            foreach ($player_names as $player_name)
            {
                $invitation['summonerid'] = $this->lol_model->get_summonerid_from_summonername($player_name);
                $invitation['teamid'] = $team['teamid'];
                $invitation['message'] = $invitations['invite_message'];

                $this->team_invite_model->invite_summoner($invitation);
            }
            if(count($player_names) == 1)
            {
                $this->system_message_model->set_message(join(', ', $player_names)  . " has been invited to " . $team['team_name']  , MESSAGE_INFO);
            }
            else
            {
                $this->system_message_model->set_message(join(', ', $player_names)  . " have been invited to " . $team['team_name']  , MESSAGE_INFO);
            }
            redirect('home', 'refresh');
        }
    }


    public function player_registered($player_list)
    {
        //for callback
        //for player_namestrim
        $player_list = trim($player_list,",");
        $player_names = explode(",", $player_list);
        $callback = explode (",", $player_list);
        $invalidnames = array();
        foreach ($player_names as $player_name)
        {
            if(!$this->lol_model->registered_summoner(trim($player_name)))
            {
               array_push($invalidnames,$player_name);
            }
        }
        if($invalidnames)
        {
            if(count($invalidnames) == 1)
            {
                $this->system_message_model->set_message(join(', ', $invalidnames)  . " is not registered in our systems."  , MESSAGE_ERROR);
                $this->form_validation->set_message('player_registered',  join(', ', $invalidnames)  . " is not registered in our systems");
            }
            else
            {
                $this->system_message_model->set_message(join(', ', $invalidnames)  . " are not registered in our systems."  , MESSAGE_ERROR);
                $this->form_validation->set_message('player_registered',  join(', ', $invalidnames)  . " are not registered in our systems");
            }
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function player_inteam($player_list)
    {
        //for callback, verifies player(s) are part of existing team
        $player_list = trim($player_list,",");
        $player_names = explode(",", $player_list);
        $invalidnames = array();
        foreach ($player_names as $player_name)
        {

            if($this->team_model->get_team_id_by_summonername(trim($player_name))) 
                array_push($invalidnames, $player_name);

            if($this->team_model->get_team_id_by_summonername(trim($summoner_name)))
                array_push($invalidnames,$summoner_name);

        }
        if($invalidnames) {
            if(count($invalidnames) == 1)
            {
                $this->system_message_model->set_message( join(', ', $invalidnames)  . " is already part of a team."  , MESSAGE_ERROR);
                $this->form_validation->set_message('player_inteam',  join(', ', $invalidnames)  . " is already part of a team.");
            }
            else
            {
                $this->system_message_model->set_message( join(', ', $invalidnames)  . " are already part of a team."  , MESSAGE_ERROR);
                $this->form_validation->set_message('player_inteam',  join(', ', $invalidnames)  . " are already part of a team.");
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
            $this->form_validation->set_message('unique_teamname','A team with an identical name already exists for this eSport.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}
