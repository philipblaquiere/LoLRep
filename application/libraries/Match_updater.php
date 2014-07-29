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
    private $CI;

	public function __construct($params)
    {      
        
        $this->teamids = $params['teamids'];
        $this->esportid = $params['esportid'];
        $this->playerid = $params['playerid'];
        if(array_key_exists('region', $params))
        {
            $this->region = $params['region'];            
        }
        $this->CI =& get_instance();
        $this->CI->load->library('lol_api');
        $this->CI->load->model('match_model');
        $params = array(self::LOL_PLAYERID => $this->playerid);
        $this->CI->load->library('match_cache',$params);
        $this->CI->load->library('match_validator', $params);
    }

    public function update()
    {
        $scheduled_matchids = $this->_get_scheduled_matches();
        $scheduled_matches = $CI->match_model->get_matches($scheduled_matchids,$this->esportid);
        if($scheduled_matchids)
        {
            switch ($this->esportid)
            {
                case '1':
                    return $this->_update_lol($scheduled_matches);
                    break;
                
                default:
                    return "No corresponding eSport found";
                    break;
            }
        }
    }

    /*
    |    Gets the matchids of matches that are scheduled, 
    |    have not yet been updated in the db,
    |    and might have been played
    */
    private function _get_scheduled_matches()
    {
        $scheduled_matchids = $this->->match_model->get_scheduled_matches($this->teamids, time(), $this->esportid);
        return $scheduled_matchids;
    }

    private function _update_lol($scheduled_matches)
    {         
        $recent_lol_matches = $this->CI->lol_api->get_recent_matches($this->playerid);
        $match_results = array();
        foreach ($scheduled_matches as $scheduled_match)
        {
            array_push($match_results, $CI->match_validator->validate($scheduled_match, $recent_lol_matches, $this->esportid));
        }
        return $match_results;

        /*foreach ($recent_matches as $recent_match)
        {
            $CI->match_cache->add_match($recent_match);
        }*/
    	//return $matches;
    }
}