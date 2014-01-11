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
        
        $season['registration_start'] = date ("Y-m-d H:i:s", strtotime($registration_start));
        $registration_end = new DateTime($registration_start);
        $registration_end->modify('+6 day');
        $startdate = new DateTime($registration_start);
        $startdate->modify('+1 week');
        $season['registration_end'] = date ("Y-m-d H:i:s", strtotime($registration_end->format('m/d/Y')));
        $season['startdate'] = date("Y-m-d H:i:s",strtotime($startdate->format('m/d/Y')));
        $season['enddate'] = date("Y-m-d H:i:s",strtotime($enddate));
        $season['name'] = $name;
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
}