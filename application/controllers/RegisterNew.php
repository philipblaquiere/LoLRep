<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RegisterNew extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('header');
		$this->load->view('Register');
		$this->load->view('footer');
	}
}