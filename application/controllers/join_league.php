<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Join_league extends MY_Controller {
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
    }

    public function join($leagueid) {
        $team = $this->team_model->get_team_by_captainid($_SESSION['user']['UserId'], $_SESSION['esportid']);
        if(!$team) {
            //user isn't captain of team.
            $this->system_message_model->set_message("You must be team captain to join a league"  , MESSAGE_ERROR);
            $this->view_wrapper('view_leagues');
            return;
        }

        $current_league = $this->league_model->get_current_league_by_teamid($team['teamid']);

        if($current_league) {
            //user is already part of team, check if team joined the league sometime today.
            $today_midnight = $this->get_default_epoch($this->get_local_datetime(strtotime('today midnight')));
            $time_now = strtotime($this->get_local_datetime(time()));
            $joined_date = strtotime($this->get_local_datetime(strtotime($current_league['joined'])));
            if($time_now - $joined_date < 86400) {
                //joined today, ask to wait.
                $this->system_message_model->set_message("You recently joined another league, you must wait 24h to switch league. You have until the registration period ends.", MESSAGE_INFO);
                redirect('view_leagues', 'refresh');
            }
            else {
                //remove from league and join the other league
                $this->league_model->leave_league($team['teamid'],$current_league['leagueid']);
                $joined_league = $this->league_model->join_league($team['teamid'],$leagueid);

                $this->system_message_model->set_message("You have successfully joined the league" , MESSAGE_INFO);
                redirect('view_leagues', 'refresh');
            }
        }
        else{
            //User is not part of team and is captain of his team, join the league
            $joined_league = $this->league_model->join_league($team['teamid'],$leagueid);
            $this->system_message_model->set_message("You have successfully joined the league" , MESSAGE_INFO);
            redirect('view_leagues', 'refresh');
        }
    }
}