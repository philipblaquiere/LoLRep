<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Esport extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('esport_model');
        $this->load->model('team_model');
    }

    public function register_LoL() {
        $this->require_login();
        $this->view_wrapper('user/register_LoL');
    }
    
}