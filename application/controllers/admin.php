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
    }

    public function index() {
        $data['result'] = array();
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
        $this->banned_model->ban_summoner($user,$reason);
        $this->system_message_model->set_message("User has been banned" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');
    }

    public function ban_summoner_by_summonername() {
        $this->load->library('form_validation');
        $summonername = $this->input->post('ban_summonername');
        $reason = $this->input->post('ban_reason');
        $user = $this->banned_model->get_summoner_by_summonername($summonername);
        $this->banned_model->ban_summoner($user,$reason);
        $this->system_message_model->set_message("User has been banned" , MESSAGE_INFO);
        $this->view_wrapper('admin_panel');
    }

    public function ban_summoner($user,$reason) {
        $this->banned_model->ban_summoner($user,$reason);
    }
}