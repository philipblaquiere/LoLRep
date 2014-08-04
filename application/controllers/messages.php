<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages extends MY_Controller{
  /**
   * Constructor: initialize required libraries.
   */
  public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('message_model');
        $this->load->model('system_message_model');
    }


    public function index()
    {
        $data['player'] = $this->get_player();
        $data['message'] = "hello";
        printf($data['player']);
        $this->view_wrapper('message_inbox', $data);
    }
}
