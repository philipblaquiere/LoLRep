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
        $data['season'] = $season;
        $data['season']['registration_start'] = $this->get_local_date($data['season']['registration_start']);
        $data['season']['registration_end'] = $this->get_local_datetime($data['season']['registration_end']);
        $data['season']['startdate'] = $this->get_local_date($data['season']['startdate']);
        $data['season']['enddate'] = $this->get_local_datetime($data['season']['enddate']);
        $data['esports'] = $esports;
        
        //Validation on input (requires that all fields exist)
        $this->load->library('form_validation');
        $this->form_validation->set_rules('leaguename', 'League Name', 'trim|required|xss_clean|callback_unique_leaguename');
        $this->form_validation->set_rules('ESport', 'ESport', 'trim|required');
        
        if($this->form_validation->run() == FALSE){
            $this->view_wrapper('create_league', $data);
        }
        else {

            $league = $this->input->post();
            $openseason = $this->season_model->get_open_season();
            $league['seasonid'] = $openseason['seasonid'];
            if($this->league_model->create_league($league))
                $this->system_message_model->set_message('The League has been created.', MESSAGE_INFO);

            redirect('home', 'refresh');
        }
    }

    public function unique_leaguename($leaguename)
    {
        $openseason = $this->season_model->get_open_season();
        $existing_league = $this->league_model->get_league_by_name($leaguename,$openseason['seasonid']);
        if($existing_league) {
            $this->form_validation->set_message('unique_leaguename','A league with an identical name already exists.');
            return false;
        }
        else {
            return true;
        }
    }
}