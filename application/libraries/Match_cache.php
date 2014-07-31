<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_cache
{
	private $esportid;
	private $playerid;
    private $last_match_index;
    private $CI;

    const SLEEP_AMOUNT = 1; //number of seconds thread will sleep in order to allow other thread to update matches.
    const SLEEP_THRESHOLD = 4; //longest wait time before timeout
    const MATCHID = "matchid";
    const GAMEID = "gameId";
    const MATCH_KEY = "matches";
    const MATCH_DETAILS = "match_details";
    const IS_MATCH_COMPLETE = "complete";

	public function __construct($params)
    {
        $this->CI =& get_instance();
        $this->CI->load->library('redis');
        $this->playerid = $params['playerid'];
        $this->last_match_index = null;
    }

    public function has_match($matchid)
    {
        return $this->CI->redis->exists($matchid);
    }

    public function get_match($matchid)
    {
        //dealing with concurrency
        if($this->has_match($matchid))
        {
            if($this->CI->redis->hget($matchid, self::IS_MATCH_COMPLETE))
            {
                return json_decode($this->CI->redis->hget($matchid, self::MATCH_DETAILS), TRUE);
            }
            else
            {
                //allow thread to update match to complete status
                $sleep_time = 0;
                while(!$this->CI->redis->hget($matchid, self::IS_MATCH_COMPLETE))
                {
                    sleep(self::SLEEP_AMOUNT);
                    $sleep_time += self::SLEEP_AMOUNT;
                    if($sleep_time >= self::SLEEP_THRESHOLD)
                    {
                        return NULL;
                    }
                }
                return json_decode($this->CI->redis->hget($matchid, self::MATCH_DETAILS), TRUE); 
            }
        }
        return NULL;
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

    public function add_match($match)
    {
        $this->CI->redis->hset($match[self::MATCHID], array(self::IS_MATCH_COMPLETE => $match[self::IS_MATCH_COMPLETE]));
        $this->CI->redis->hset($match[self::MATCHID], array(self::MATCH_DETAILS => json_encode($match)));
    }
}
