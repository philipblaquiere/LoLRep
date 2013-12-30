<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Add_esport extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('system_message_model');
        $this->load->model('esport_model');
    }
    public function index() {
        $this->require_login();
        $esports = $this->esport_model->get_all_esports();
        $data['esports'] = $esports;
        $this->view_wrapper('user/add_esport',$data);
    }

     public function register_LoL() {
        $this->require_login();
        $this->view_wrapper('user/register_LoL');
    }

}
