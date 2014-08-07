<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Esport extends MY_Controller{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
    {
        
    }

    public function add()
    {
        
    }

    public function register_LoL()
    {
        $this->require_login();
        $this->view_wrapper('user/register_LoL', array(), false);
    }
    
}