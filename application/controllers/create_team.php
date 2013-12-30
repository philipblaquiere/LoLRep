<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_team extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('system_message_model');
        $this->load->model('esport_model');
        $this->load->model('team_model');
        $this->load->model('lol_model');

    }

    public function index() {
        $this->require_login();
        //Retrieve all esports registered by user.
        $esports = $this->esport_model->get_all_registered_esports($_SESSION['user']['UserId']);

        if(!$esports) {
            $data['esports'] = $this->esport_model->get_all_esports();
            $this->system_message_model->set_message("Add an Esport to your account before creating a team!"  , MESSAGE_ERROR);
            $this->view_wrapper('user/add_esport',$data);
            return;
        }

        $data['esports'] = $esports;

        $this->load->library('form_validation');

        $this->form_validation->set_rules('esportid', 'ESport', 'required|callback_has_team');
        $this->form_validation->set_rules('teamname', 'Team Name', 'trim|required|xss_clean|callback_unique_teamname');

        if($this->form_validation->run() == FALSE) {
            $this->view_wrapper('user/create_team', $data);
        }
        else {
            $team['name'] = $this->input->post('teamname');
            $team['esportid'] = $this->input->post('esportid');
            $make_captain = $this->input->post('make_captain');

            $captain = $_SESSION['user'];
            if($team['esportid'] == 1) {
                //Game is league of legends, get summonerid
                $captain['gameid'] = $this->lol_model->get_summonerid_from_uid($captain['UserId']);
            }
            $this->team_model->create_team($team,$captain);
            $this->system_message_model->set_message($team['name'] . ' has been created, add people to your team' , MESSAGE_INFO);
            
            redirect('home', 'location');
        }
    }

    public function unique_teamname($teamname) {
        $existing_team = $this->team_model->get_team_by_name($teamname, $this->input->post('esportid'));
        if($existing_team) {
            $this->system_message_model->set_message("A team with an identical name already exists."  , MESSAGE_ERROR);
            $this->form_validation->set_message('unique_teamname','A team with an identical name already exists.');
            return false;
        }
        else {
            return true;
        }
    }

    public function has_team() {
        $teams = $this->team_model->get_all_teams_by_uid($_SESSION['user']['UserId']);
        foreach ($teams as $team) {
            if($team['esportid'] == $this->input->post('esportid')) {
                $this->system_message_model->set_message("You can be registered to one team per Esport at a time. You're currently part of team : " . $team['name'] , MESSAGE_ERROR);
                $this->form_validation->set_message('has_team','You can be registered to a single team per Registered Esport.');
                return false;
            }
            else {
                return true;
            }
        }
    }
}