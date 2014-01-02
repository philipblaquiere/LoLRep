<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Trade_lol_model extends CI_Model {

	public function __construct() {
	    parent::__construct();
	    $this->db1 = $this->load->database('default', TRUE);
 	}
 }

