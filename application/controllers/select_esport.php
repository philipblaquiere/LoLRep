<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Select_esport extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('header');
		$this->load->view('SelectESport');
		$this->load->view('footer');
	}
}