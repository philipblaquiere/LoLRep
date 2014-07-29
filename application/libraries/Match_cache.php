<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_cache
{
	private $esportid;
	private $playerid;
    private $last_match_index;

    const MATCHID = "matchid";
    const GAMEID = "gameId";
    const MATCH_KEY = "matches";

	public function __construct($params)
    {
        
        $this->playerid = $params['playerid'];
        $this->last_match_index = null;
    }

    public function get_match($matchid)
    {
        return $this->_has_match($matchid);
    }

    public function get_next_matches($match_load_count = 10)
    {
        $return_matches = array();
        if($this->last_match_index == NULL)
        {
            $this->last_match_index = 0;
        }
        for ($i=$this->last_match_index; $i < $match_load_count; $i++)
        { 
            
        }
    }

    private function _has_match($matchid)
    {
        $CI =& get_instance();
        $CI->load->library('redis');
        $match = $CI->redis->get($matchid));
        if($match)
        {
            return json_decode($match);
        }
        else
        {
            return NULL;
        }
    }

    public function add_match($new_match)
    {
        $CI =& get_instance();
        $CI->load->library('redis');

        //check if match is already set in redis db
        $existing_match = $this->_has_match($new_match[self::MATCHID]);
        if($existing_match == NULL)
        {
            //match doesn't exist
            $CI->redis->set($new_match[self::MATCHID], json_encode($new_match));
        }
    }
}
