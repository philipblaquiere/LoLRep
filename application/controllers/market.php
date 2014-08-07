<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Market extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
    {
        parent::__construct();
        $this->load->model('system_message_model');
        $this->load->model('team_model');
    }

    public function index()
    {
        $this->require_login();
        $this->view_wrapper('market', array(), false);
    }

}