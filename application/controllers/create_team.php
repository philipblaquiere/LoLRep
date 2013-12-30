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
        $esports = $this->esport_model->get_all_esports();
        $data['esports'] = $esports;

        $this->load->library('form_validation');

        $this->form_validation->set_rules('esportid', 'ESport', 'required|callback_user_registered');
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
            $this->system_message_model->set_message($team['name'] . ' has been created, add people to your team' , MESSAGE_ERROR);
            
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

    public function user_registered() {
        $user = $_SESSION['user'];
        $esportid = $this->input->post('esportid');

        switch ($esportid) {
            case '1':
                //Check if user is registered for League of Legends
                $summonername = $this->lol_model->get_summonername_from_uid($user['UserId']);
                if(!$summonername) {
                    $this->form_validation->set_message('user_registered','Your are not registered for that Esport');
                    $this->system_message_model->set_message("Add an Esport to your account before creating a team!"  , MESSAGE_ERROR);
                    return false; 
                }
                else {
                    return true;
                }
                break;
                        
            default:
                # code...
                break;
        }
    }
}