<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
    {
        parent::__construct();
        $this->load->model('system_message_model');
        
    }

    public function index()
    {
        $this->require_login();
        $data['player'] = $this->get_player();
        $data['is_logged_in'] = $this->is_logged_in();
        $this->load->library('lol_api');

        $player = $this->get_player();
        $teamids = $player['teams'];
        $params = array('teamids' => $teamids, 'esportid' => $this->get_esportid(), 'playerid' => $player['playerid'], 'region' =>$player['region']);
        $this->load->library('match_aggregator', $params);
        $matches = $this->match_aggregator->aggregate_matches();
        print_r($matches);
        
        
        
        //$response = $this->match_updater->update();
        //$this->match_model->update_matches($dirty_matches);
        //$this->statistics_model->add_match_stats($dirty_matches,$this->get_esportid());
        //Update the dirty bit to FALSE
        /*$dirty_matchids = array();
        foreach ($dirty_matches as $matchid => $match) 
        {
            array_push($dirty_matchids, $matchid);
        }*/
        //print_r($response);
        //$this->match_cache->mark_dirty($dirty_matchids, FALSE);
        //print_r($response);
        //$this->match_model->update_match($response);
        //print_r($this->statistics_model->add_match_stats($response, $this->get_esportid()));

       /* $recent_lol_matches = array();
        $recent_lol_matches = $this->lol_api->get_recent_matches($player['playerid']);
        print_r($recent_lol_matches);*/

        $this->load->view('include/header', $data);
        $this->load->view('profile_header', $data);
        $this->load->view('include/navigation', $data);
        $this->load->view('profile', $data);
        $this->load->view('include/footer');
    }
}