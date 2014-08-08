<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Market_signUp extends MY_Controller
{
  /**
   * Constructor: initialize required libraries.
   */
  public function __construct()
    {
      parent::__construct();
      $this->load->model('system_message_model');
      $this->load->model('team_model');
      $this->load->model('player_model');
      $this->load->model('market_model');
    }

    public function index()
    {

      //TODO
      //GET PLAYER ID AND INFO AND SEND IT TO THE VIEW
      $this->require_login();
      $this->view_wrapper('market_signup', array(), false);
    }



}
