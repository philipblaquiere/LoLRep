<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_aggregator
{
	private $CI;
	private $params;

	const MATCHID = 'matchid';
	const PLAYERID = 'playerid';
	const NON_DIRTY = 'FALSE';

	public function __construct($params)
    {
    	$this->params = $params;
        $this->CI =& get_instance();
        $this->CI->load->library('match_cache');
        $this->CI->load->library('match_updater', $params);
        $this->CI->load->model('match_model');
    }

    public function aggregate_matches()
    {
    	//return array containing new and existing matches
    	$aggregated_matches = array();

    	//Get matches which may by in the db;
    	$new_matches = $this->CI->match_updater->update();
    	if(!array_key_exists('error', $new_matches))
    	{
    		foreach ($new_matches as $new_match)
	    	{
	    		$aggregated_matches[$new_match[self::MATCHID]] = $new_match;
	    	}
    	}
    	
    	$finished_matchids = $this->_get_finished_matches($this->params[self::PLAYERID], $this->params['esportid']);


    	//Get already cached matches associated to finished match.
    	foreach ($finished_matchids as $finished_matchid )
    	{
    		$aggregated_matches[$finished_matchid] = $this->CI->match_cache->get_match($finished_matchid);
    	}

    	$matchids_to_get = array();
    	foreach ($aggregated_matches as $aggregated_matchid => $aggregated_match)
    	{
    		if(empty($aggregated_match))
    		{
    			array_push($matchids_to_get, $aggregated_matchid);
    		}
    	}

    	$matches =  $this->CI->match_model->get_matches($matchids_to_get, $this->params['esportid']);
    	//Add NON_DIRTY matches to match_cache;
    	foreach ($matches as $match)
    	{
    		$aggregated_matches[$match[self::MATCHID]] = $match;
    	}
        $this->CI->match_cache->add_matches($matches, self::NON_DIRTY);
        return $aggregated_matches;
    }



    private function _get_finished_matches($playerid, $esportid)
    {
    	return $this->CI->match_model->get_finished_matchids($playerid, $esportid);
    }
}