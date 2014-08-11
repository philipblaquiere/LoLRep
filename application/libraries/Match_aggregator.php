<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_aggregator
{
	private $CI;
	private $params;

	const MATCHID = 'matchid';
	const PLAYERID = 'playerid';
	const NON_DIRTY = 'FALSE';
    const ESPORTID = 'esportid';
    const TEAM = 'team';
    const SEASONID = 'seasonid';

	public function __construct($params)
    {
    	$this->params = $params;
        $this->CI =& get_instance();
        $this->CI->load->library('match_cache');
        $this->CI->load->library('match_updater', $params);
        $this->CI->load->model('match_model');
    }

    public function get_recent_matches()
    {
        //return array containing new and existing matches
        $aggregated_matches = array();

        //Get matches which may by in the db;
        $new_matches = $this->CI->match_updater->update();
        if(!array_key_exists('error', $new_matches) && !empty($new_matches))
        {
            foreach ($new_matches as $new_match)
            {
                $aggregated_matches[$new_match[self::MATCHID]] = $new_match;
            }
        }
        $finished_matchids = $this->_get_finished_matches();
        //Get already cached matches associated to finished match.
        $matchids_to_get = array();
        foreach ($finished_matchids as $finished_matchid)
        {
            if($this->CI->match_cache->has_match($finished_matchid))
            {
                $aggregated_matches[$finished_matchid] = $this->CI->match_cache->get_match($finished_matchid);
            }
            else
            {
                if(isset($this->params[self::PLAYERID]))
                {
                    array_push($matchids_to_get, $finished_matchid);
                }
                else
                {
                    array_push($matchids_to_get, $finished_matchid['matchid']);
                }
                
            }
        }

        if(!empty($matchids_to_get))
        {
            $matches =  $this->CI->match_model->get_matches($matchids_to_get, $this->params[self::ESPORTID]);
            foreach ($matches as $match)
            {
                if(!empty($match))
                {
                    $aggregated_matches[$match[self::MATCHID]] = $match;
                }
            }
            //Add NON_DIRTY matches to match_cache;
            $this->CI->match_cache->add_matches($matches, self::NON_DIRTY);
        }
        
        return $aggregated_matches;
    }

    public function get_upcoming_matches()
    {
        $upcoming_matchids = $this->CI->match_model->get_upcoming_matchids($this->params[self::PLAYERID], $this->params[self::ESPORTID]);
        $upcoming_matches = array();
        foreach ($upcoming_matchids as $matchid)
        {
            //tell cache to not wait for the match update.
            $wait_for_update = FALSE;
            $upcoming_matches[$matchid] = $this->CI->match_cache->get_match($matchid, $wait_for_update);
        }

        $matchids_to_get = array();
        foreach ($upcoming_matches as $upcoming_matchid => $upcoming_match)
        {
            if(empty($upcoming_match))
            {
                array_push($matchids_to_get, $upcoming_matchid);
            }
        }
        $matches =  $this->CI->match_model->get_matches($matchids_to_get, $this->params[self::ESPORTID]);
        foreach ($matches as $match)
        {
            $upcoming_matches[$match[self::MATCHID]] = $match;
        }
        $this->CI->match_cache->add_matches($matches, self::NON_DIRTY);

        return $upcoming_matches;
    }

    private function _get_finished_matches()
    {   
        if(isset($this->params[self::PLAYERID]))
        {
            return $this->CI->match_model->get_finished_matchids($this->params[self::PLAYERID], $this->params[self::SEASONID], $this->params[self::ESPORTID]);
        }
        elseif(isset($this->params[self::TEAM]))
        {
            return $this->CI->match_model->get_finished_matchids_byteam($this->params[self::TEAM]['teamid'], $this->params[self::SEASONID], $this->params[self::ESPORTID]);
        }
    }
}