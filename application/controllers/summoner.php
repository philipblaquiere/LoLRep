<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Summoner extends MY_Controller{
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
        $this->load->model('trade_lol_model');
    }

    public function create() {
        $this->require_login();
        if(!$_SESSION['summoner']) {
          //global object not present, an error has occured while checking rune pages or while redirecting here from JQuery
          $this->system_message_model->set_message('Error: No Summoner has been found. Cannot complete registration', MESSAGE_INFO);
          redirect('user/register_LoL', 'location');
        }
        else {
          //valid summoner, create summoner and redirect to home page.
          $this->lol_model->create_summoner($_SESSION['uid'], $_SESSION['summoner']);
          $this->system_message_model->set_message($_SESSION['summoner']['name'] . ', you have successfully linked your League of Legends account!', MESSAGE_INFO);
          unset($_SESSION['summoner']);
          $this->view_wrapper('home');
        }
    }
}