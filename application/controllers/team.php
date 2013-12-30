<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends MY_Controller{
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
        $this->load->model('lol_model');
        $this->load->model('team_model');
    }
    public function teams() {
    $this->require_login();
    $this->view_wrapper('user/teams');
    }

    public function create_team() {
        $this->require_login();
        $esports = $this->esport_model->get_all_esports();
        $data['esports'] = $esports;
        $this->view_wrapper('user/create_team', $data);
    }

    public function create_team_submit() {
        $this->require_login();

        //Validation on input (requires that all fields exist)
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');


        $this->form_validation->set_rules('esportid', 'ESport', 'required');
        $this->form_validation->set_rules('teamname', 'Team Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('make_captain', 'Make Captain', 'required');

        $team['name'] = $this->input->post('teamname');
        $team['esportid'] = $this->input->post('esportid');
        $make_captain = $this->input->post('make_captain');

        if($this->form_validation->run() == FALSE){
          $this->system_message_model->set_message('Team Creation failed. ', MESSAGE_ERROR);
          redirect('user/create_team', 'location');
        }
        $existing_team = $this->team_model->get_team_by_name($team['name'], $team['esportid']);
        if($existing_team) {
          //team already exists.
          $this->system_message_model->set_message('A team with an identical name already exists.', MESSAGE_ERROR);
          redirect('user/create_team', 'location');
        }
        else {
            //create team with logged in user as captain
            if($make_captain) {
                $captain = $_SESSION['user'];
                $this->team_model->create_team($team,$captain);
                $this->system_message_model->set_message($team['name'] . ' has been created, add people to your team' , MESSAGE_INFO);
                redirect('home', 'location');
            }
            else {
            //make team without captain
                $captain = $_SESSION['user'];
                $this->team_model->create_team($team,$captain);
                $this->system_message_model->set_message($team['name'] . ' has been created, add people to your team' , MESSAGE_INFO);
                redirect('home', 'location');
            }
        }
    }

  public function join_team() {
    $this->require_login();
  }
}