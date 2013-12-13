<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register_LoL extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('header');
		$this->load->view('RegisterLeagueOfLegends');
		$this->load->view('footer');
	}
}