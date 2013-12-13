<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->helper('url');

		$this->load->view('include/header');
		$this->load->view('include/navigation');
		$this->load->view('home');
		$this->load->view('include/footer');
	}
}