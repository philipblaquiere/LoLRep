<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_aggregator
{

	private $esportid;
	private $playerid;
	private $region;

	public function __construct($params)
    {
        $esportid = $params['esportid'];
        $playerid = $params['playerid'];
        $region = $params['region'];
    }

    public function update()
    {
    	switch ($this->esportid) {
    		case '1':
    			return $this->update_lol();
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function update_lol()
    {
    	$this->load->library('lol_api');

    	$recent_lol_matches = $this->lol_api->get_recent_matches($this->playerid);

    	return $recent_lol_matches;
    }
}