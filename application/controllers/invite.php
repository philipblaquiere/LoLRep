<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invite extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
    {
        parent::__construct();
        $this->load->model('system_message_model');
        $this->load->model('esport_model');
        $this->load->model('team_model');
        $this->load->model('lol_model');
        $this->load->model('team_invite_model');
    }

    public function invite($teamid)
    {
        $team = $this->team_model->get_team_by_teamid($teamid);
        if($team['esportid'] == 1)
        {
            $this->invite_lol($team);
        }
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
            $this->view_wrapper('team_invite_lol',$data, false);
        }
        else
        {
            $invitations = $this->input->post();
            $invitation = array();
            $summonernames = explode(",", trim($invitations['summonerlist'],","));
            foreach ($summonernames as $summonername)
            {
                $invitation['summonerid'] = $this->lol_model->get_summonerid_from_summonername($summonername);
                $invitation['teamid'] = $team['teamid'];
                $invitation['message'] = $invitations['invite_message'];
                
                $this->team_invite_model->invite_summoner($invitation);
            }
            if(count($summonernames) == 1)
            {
                $this->system_message_model->set_message(join(', ', $summonernames)  . " has been invited to " . $team['name']  , MESSAGE_INFO);
            }
            else
            {
                 $this->system_message_model->set_message(join(', ', $summonernames)  . " have been invited to " . $team['name']  , MESSAGE_INFO);
            }
            redirect('home', 'refresh');
        }
    }

    /*
    * User accepts team invite, check to see if user is part of team, 
    * Take user away from exisiting team and add to new team.
    */
    public function accept_invite($inviteid)
    {
        //get invite details
        $invite = $this->team_invite_model->get_invite_byid($inviteid);
        $esport = $this->esport_model->get_esport_byid($_SESSION['esportid']);
        //check to see if team still has space.
        $numplayers = $this->team_model->get_team_by_teamid($invite['teamid'], $esportid);

        if(count($numplayers) == $esport['max_players'])
        {
            $this->system_message_model->set_message('This team is full ('. $esport['max_players'] . ' players), contact the team captain for further information'  , MESSAGE_INFO);
            redirect('teams', 'refresh');
        }
        //update invite model
        $this->team_invite_model->mark_invite_accepted($inviteid,$_SESSION['esportid']);
        //check to see if user is presently in team, if so remove from current and add new
        $currentteam = $this->team_model->get_lol_teamname_by_uid($_SESSION['user']['UserId']);
        if($currentteam)
        {
            //Should have user confirmation panel, Are you sure? Y/N type...
            $this->team_model->remove_summoner_from_team($invite['summonerid']);
        }
        //add player to new team
        $this->team_model->add_summoner_to_team($invite['teamid'], $invite['summonerid']);
        $teamname = $this->team_model->get_teamname_by_teamid($invite['teamid']);
        $this->system_message_model->set_message('You have joined ' . $teamname['name'] . ', say "Hi!" to your new teammates!'  , MESSAGE_INFO);
        redirect('teams', 'refresh');
    }

    public function decline_invite($inviteid)
    {
        //get invite details
        $invite = $this->team_invite_model->get_invite_byid($inviteid);
        //update invite model
        $this->team_invite_model->mark_invite_declined($inviteid,$_SESSION['esportid']);
        $teamname = $this->team_model->get_teamname_by_teamid($invite['teamid']);
        $this->system_message_model->set_message('You have declined the offer from ' . $teamname['name'] , MESSAGE_INFO);
        redirect('teams', 'refresh');
    }
}