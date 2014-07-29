<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_updater
{
    const LOL_PLAYERID_PREFIX = "summonerID";
    const LOL_PLAYERID = "playerid";
    const LOL_TEAMID_PREFIX ="teamId";
    const LOL_GAMEID_PREFIX = "gameId";
    const LOL_GAMEID = "matchid";
    const LOL_GAMEDATE_PREFIX = "createDate";
    const LOL_GAMEDATE = "match_date";
    const LOL_GAMETYPE_PREFIX = "gameType";
    const LOL_GAMEMODE_PREFIX = "gameMode";
    const LOL_MAPID_PREFIX = "mapId";
    const LOL_GAMEMODE_MODE = "CLASSIC";
    const LOL_GAMETYPE_TYPE = "CUSTOM_GAME";
    const LOL_MAPID_MAPID = "1";

	private $esportid;
	private $playerid;
	private $region;
    private $teamids;

	public function __construct($params)
    {
        $this->teamids = $params['teamids'];
        $this->esportid = $params['esportid'];
        $this->playerid = $params['playerid'];
        if(array_key_exists('region', $params))
        {
            $this->region = $params['region'];            
        }
    }

    public function update()
    {
    	switch ($this->esportid)
        {
    		case '1':
    			return $this->_update_lol();
    			break;
    		
    		default:
    			return "No corresponding eSport found";
    			break;
    	}
    }

    private function _update_lol()
    {         
    	$CI =& get_instance();
        $CI->load->library('lol_api');
        $CI->load->library('match_validator', $params);
        $params = array(self::LOL_PLAYERID => $this->playerid);
        $CI->load->library('match_cache',$params);

        
        $recent_matches = $CI->lol_api->get_recent_matches($this->playerid);
        $recent_matches = $recent_matches['games'];
        foreach ($recent_matches as $recent_match)
        {
            /*if(array_key_exists($recent_match[self::LOL_GAMEID_PREFIX], $loaded_matches))
            {
                if($recent_match[self::LOL_GAMETYPE_PREFIX] == self::LOL_GAMETYPE_TYPE
                    && $recent_match[self::LOL_GAMEMODE_PREFIX] == self::LOL_GAMEMODE_MODE
                    && $recent_match[self::LOL_MAPID_PREFIX] == self::LOL_MAPID_MAPID)
                {
                    //Match is valid, check team composition
                    
                }


            }*/
            $CI->match_cache->add_match($recent_match);
        }
        //$loaded_matches = $CI->match_cache->get_loaded_matches($this->esportid);
    	//return $matches;
    }
}