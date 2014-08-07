<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_cache
{
    private $CI;

    const MATCH_CACHE_LIMIT = 100000; //Maximum number of cached matches.
    const MATCH_LIFECYCLE_DURATION = 129600; // number of seconds a match is allowed to stay in Redis without being accessed
    const SLEEP_AMOUNT = 1; //number of seconds thread will sleep in order to allow other thread to update matches.
    const SLEEP_THRESHOLD = 4; //longest wait time before timeout
    const LAST_ACCESS_KEY = "last_access";
    const DIRTY_BIT = "dirty";
    const ACCESS_COUNT = "access_count";
    const MATCHID = "matchid";
    const GAMEID = "gameId";
    const MATCHIDS_KEY = "matchids";
    const MATCH_DETAILS = "match_details";
    const IS_MATCH_COMPLETE = "complete";

	public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('redis');
    }

    public function has_match($matchid)
    {
        return $this->CI->redis->sismember(self::MATCHIDS_KEY, $matchid);
    }
    private function _update_access($matchid)
    {
        $this->CI->redis->hincrby($matchid, array(self::ACCESS_COUNT => 1));
        $this->CI->redis->hset($matchid, array(self::LAST_ACCESS_KEY => time()));
    }
    public function get_match($matchid, $wait_for_complete = TRUE)
    {
        //dealing with concurrency
        $can_return = TRUE;
        if($this->has_match($matchid))
        {
            if(!$this->CI->redis->hget($matchid, self::IS_MATCH_COMPLETE) && $wait_for_complete)
            {   
                //allow thread to update match to complete status
                $sleep_time = 0;
                $can_return = FALSE;
                while(!$this->CI->redis->hget($matchid, self::IS_MATCH_COMPLETE))
                {
                    sleep(self::SLEEP_AMOUNT);
                    $sleep_time += self::SLEEP_AMOUNT;
                    if($sleep_time >= self::SLEEP_THRESHOLD)
                    {
                        return NULL;
                    }
                }
                $can_return = TRUE;
            }
            if($can_return)
            {
                $this->_update_access($matchid);
                return json_decode($this->CI->redis->hget($matchid, self::MATCH_DETAILS), TRUE);
            }
        }
        return NULL;
    }

    /*
    |   Returns array of completed matches
    */
    public function get_matches($matchids, $is_complete = TRUE)
    {
        $matches = array();
        foreach ($matchids as $matchid)
        {
            if($this->CI->redis->hget($matchid, self::IS_MATCH_COMPLETE))
            {
                $this->_update_access($matchid);
                array_push($matches, json_decode($this->CI->redis->hget($matchid, self::MATCH_DETAILS), TRUE));
            }
        }
        return $matches;
    }

    public function mark_dirty($matchids, $is_dirty = TRUE)
    {
        foreach ($matchids as $matchid)
        {
            if($this->has_match($matchid))
            {
                $this->CI->redis->hset($matchid, array(self::DIRTY_BIT => $is_dirty));
            }
        }
    }

    public function remove_match($matchid)
    {
        $this->CI->redis->del($matchid);
        $this->CI->redis->srem(self::MATCHIDS_KEY, $matchid);
    }

    public function add_matches($matches, $is_dirty = TRUE)
    {
        foreach ($matches as $match)
        {
            if(!$this->has_match($match[self::MATCHID]))
            {
                $this->CI->redis->sadd(self::MATCHIDS_KEY, $match[self::MATCHID]);
            }
            $this->mark_dirty(array($match[self::MATCHID]),$is_dirty);
            $this->CI->redis->hset($match[self::MATCHID], array(self::IS_MATCH_COMPLETE => $match[self::IS_MATCH_COMPLETE]));
            $this->CI->redis->hset($match[self::MATCHID], array(self::ACCESS_COUNT => 0));
            $this->CI->redis->hset($match[self::MATCHID], array(self::MATCH_DETAILS => json_encode($match)));
            $this->_update_access($match[self::MATCHID]);
        }
    }

    /*
    |   Called by cron_match as a scheduled task
    |   Removes all matches that haven't been accessed
    |   within the match lifecycle duration and aren't dirty.
    |   Automatically called when match cache limit is reached.
    */
    public function clean()
    {
        $matchids = $this->CI->redis->smembers(self::MATCHIDS_KEY);
        foreach ($matchids as $matchid)
        {
            $dirty = $this->CI->redis->hget($matchid, self::DIRTY_BIT);
            $complete = $this->CI->redis->hget($matchid, self::IS_MATCH_COMPLETE);
            $last_access = $this->CI->redis->hget($matchid, self::LAST_ACCESS_KEY);
            if(!$dirty && $complete && (time() - $last_access) >= self::MATCH_LIFECYCLE_DURATION)
            {
                $this->remove_match($matchid);
            }
        }
    }

    public function get_dirty_matches()
    {
        $matchids = $this->CI->redis->smembers(self::MATCHIDS_KEY);
        $dirty_matches = array();
        foreach ($matchids as $matchid)
        {
            $dirty = $this->CI->redis->hget($matchid, self::DIRTY_BIT);
            if($dirty)
            {
                $match = json_decode($this->CI->redis->hget($matchid, self::MATCH_DETAILS), TRUE);
                $dirty_matches[$matchid] = $match;
            }
        }
        return $dirty_matches;
    }
}
