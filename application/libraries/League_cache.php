<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class League_cache
{
    private $CI;

    const LEAGUE_LIFECYCLE_DURATION = 129600; // number of seconds a league is allowed to stay in Redis without being accessed
    const SLEEP_AMOUNT = 1; //number of seconds thread will sleep in order to allow other thread to update leagues.
    const SLEEP_THRESHOLD = 4; //longest wait time before timeout
    const LAST_ACCESS_KEY = "last_access";
    const DIRTY_BIT = "dirty";
    const ACCESS_COUNT = "access_count";
    const LEAGUEID = "leagueid";
    const LEAGUEIDS_KEY = "leagueids";
    const LEAGUE_NUM_TEAMS_KEY = "num_teams";
    const LEAGUE_MAX_TEAMS_KEY = "max_teams";
    const LEAGUE_FIRST_MATCHES_KEY = "first_matches";
    const LEAGUE_IS_INVITE_KEY = "invite";
    const LEAGUE_IS_PRIVATE_KEY = "private";
    const LEAGUE_DETAILS = "league_details";
    const LEAGUE_NAME_KEY = "league_name";
    const LEAGUE_TYPE_KEY = "league_typeid";
    const LEAGUE_CURRENT_SEASON_KEY = "current_season";
    const LEAGUE_SEASONS_KEY = "seasons";
    const LEAGUE_TEAMS_KEY = "teams";
    const LEAGUE_NOT_FULL = "league_not_full";
    const LEAGUE_NOT_EMPTY = "league_not_empty";
    const SEARCH_STRING = "search_text";

	public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('redis');
    }

    public function has_league($leagueid)
    {
        return $this->CI->redis->sismember(self::LEAGUEIDS_KEY, $leagueid);
    }

    private function _update_access($leagueid)
    {
        $this->CI->redis->hincrby($leagueid, array(self::ACCESS_COUNT => 1));
        $this->CI->redis->hset($leagueid, array(self::LAST_ACCESS_KEY => time()));
    }

    public function mark_dirty($leagueids, $is_dirty = TRUE)
    {
        foreach ($leagueids as $leagueid)
        {
            $this->CI->redis->hset($leagueid, array(self::DIRTY_BIT => $is_dirty));
        }
    }

    public function get_leagues($leagueids)
    {
        $leagues = array();
        foreach ($leagueids as $leagueid)
        {
            if($this->has_league($leagueid))
            {
                $this->_update_access($leagueid);
                $leagues[$leagueid] = json_decode($this->CI->redis->hget($leagueid, self::LEAGUE_DETAILS), TRUE);
            }
        }
        return $leagues;
    }

    public function add_leagues($leagues, $is_dirty = FALSE)
    {
        foreach ($leagues as $league)
        {
            if(!$this->has_league($league[self::LEAGUEID]))
            {
                $this->CI->redis->sadd(self::LEAGUEIDS_KEY, $league[self::LEAGUEID]);
            }
            $this->mark_dirty(array($league[self::LEAGUEID]),$is_dirty);
            $this->CI->redis->hset($league[self::LEAGUEID], array(self::ACCESS_COUNT => 0));
            $this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_DETAILS => json_encode($league)));
            $this->_update_access($league[self::LEAGUEID]);
            $num_teams = isset($league[self::LEAGUE_SEASONS_KEY][$league[self::LEAGUE_CURRENT_SEASON_KEY]][self::LEAGUE_TEAMS_KEY]) ? count($league[self::LEAGUE_SEASONS_KEY][$league[self::LEAGUE_CURRENT_SEASON_KEY]][self::LEAGUE_TEAMS_KEY]) : 0;
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_MAX_TEAMS_KEY => $league[self::LEAGUE_MAX_TEAMS_KEY]));
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_NUM_TEAMS_KEY => $num_teams));
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_FIRST_MATCHES_KEY => json_encode($league[self::LEAGUE_SEASONS_KEY][$league[self::LEAGUE_CURRENT_SEASON_KEY]][self::LEAGUE_FIRST_MATCHES_KEY])));
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_NAME_KEY => $league[self::LEAGUE_NAME_KEY]));
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_TYPE_KEY => $league[self::LEAGUE_TYPE_KEY]));
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_IS_INVITE_KEY => $league[self::LEAGUE_IS_INVITE_KEY]));
	    	$this->CI->redis->hset($league[self::LEAGUEID], array(self::LEAGUE_DETAILS => json_encode($league)));
        }
    }

    public function search($params)
    {
    	$leagueids = $this->CI->redis->smembers(self::LEAGUEIDS_KEY);
    	$search_results = array();
        $league_not_full = $params[self::LEAGUE_NOT_FULL];
        $league_not_empty = $params[self::LEAGUE_NOT_EMPTY];
        $invite_only = $params[self::LEAGUE_IS_INVITE_KEY];
        foreach ($leagueids as $leagueid)
        {
        	$false_hits = 0;
        	if ($params[self::SEARCH_STRING] != "" && strrpos(strtolower($this->CI->redis->hget($leagueid, self::LEAGUE_NAME_KEY)), trim(strtolower($params[self::SEARCH_STRING]))) == FALSE)
        	{
        		$false_hits+=1;
        	}
        	/*if(isset($params[self::LEAGUE_TYPE_KEY]) && $this->CI->redis->hget($leagueid, self::LEAGUE_TYPE_KEY) != $params[self::LEAGUE_TYPE_KEY])
        	{
        		$false_hits += 1;
        	}*/
        	if($league_not_full == 'true' && $this->CI->redis->hget($leagueid, self::LEAGUE_NUM_TEAMS_KEY) == $this->CI->redis->hget($leagueid, self::LEAGUE_MAX_TEAMS_KEY))
        	{
                $false_hits += 1;
        	}
        	if($league_not_empty == 'true' && $this->CI->redis->hget($leagueid, self::LEAGUE_NUM_TEAMS_KEY) == 0)
            {
                $false_hits += 1;
        	}
        	if($invite_only == 'true'&& $this->CI->redis->hget($leagueid, self::LEAGUE_IS_INVITE_KEY) == 0)
        	{
                $false_hits += 1;
        	}
        	if($false_hits == 0)
        	{
        		array_push($search_results, json_decode($this->CI->redis->hget($leagueid, self::LEAGUE_DETAILS), TRUE));
        	}
           // array_push($search_results, $error);
        }
       /* array_push($search_results, "no full :". $params[self::LEAGUE_NOT_FULL] );
        array_push($search_results, "no empty :". $params[self::LEAGUE_NOT_EMPTY] );
        array_push($search_results, "invite :". $params[self::LEAGUE_IS_INVITE_KEY] );*/
        return $search_results;
    }


     /*
    |   Called by cron_league as a scheduled task
    |   Removes all leagues that haven't been accessed
    |   within the league lifecycle duration and aren't dirty.
    |   Automatically called when league cache limit is reached.
    */
    public function clean()
    {
        $leagueids = $this->CI->redis->smembers(self::LEAGUEIDS_KEY);
        foreach ($leagueids as $leagueid)
        {
            $dirty = $this->CI->redis->hget($leagueid, self::DIRTY_BIT);
            $last_access = $this->CI->redis->hget($leagueid, self::LAST_ACCESS_KEY);
            if(!$dirty && $complete && (time() - $last_access) >= self::LEAGUE_LIFECYCLE_DURATION)
            {
                $this->remove_league($leagueid);
            }
        }
    }

    public function remove_league($leagueid)
    {
        $this->CI->redis->del($leagueid);
        $this->CI->redis->srem(self::LEAGUEIDS_KEY, $leagueid);
    }

    public function get_dirty_leagues()
    {
        $leagueids = $this->CI->redis->smembers(self::LEAGUEIDS_KEY);
        $dirty_leagues = array();
        foreach ($leagueids as $leagueid)
        {
            $dirty = $this->CI->redis->hget($leagueid, self::DIRTY_BIT);
            if($dirty)
            {
                $league = json_decode($this->CI->redis->hget($leagueid, self::LEAGUE_DETAILS), TRUE);
                $dirty_leagues[$leagueid] = $league;
            }
        }
        return $dirty_leagues;
    }

}