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

    public function get_next_matches($match_load_count = 10)
    {
        $return_matches = array();
        if($this->last_match_index == NULL)
        {
            $this->last_match_index = 0;
        }
        for ($i=$this->last_match_index; $i < $match_load_count; $i++)
        { 
            //$match = $_SESSION['']
            //array_push($return_matches, )
        }
    }

    private function _has_match($matchid)
    {
        $CI =& get_instance();
        $CI->load->library('redis');
        $match = $CI->redis->HGET(array(self::MATCH_KEY, $matchid));
        if($match)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function add_match($new_match)
    {
        $CI =& get_instance();
        $CI->load->library('redis');

        //check if match is already set in redis db
        $response = $CI->redis->get($new_match[self::MATCHID]);
        $existing_match = json_decode($response);
        if(!$existing_match)
        {
            //match doesn't exist
            $CI->redis->set($new_match[self::MATCHID], $new_match);
        }
        
        /*if(!$this->_has_match($new_match[self::GAMEID]))
        {
            $CI->redis->HSET(array(self::MATCH_KEY, $new_match[self::GAMEID]));
        }*/
    }
}
