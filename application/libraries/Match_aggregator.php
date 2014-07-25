<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_aggregator
{
    const LOL_GAMETYPE_PREFIX = "gameType";
    const LOL_GAMEMODE_PREFIX = "gameMode";
    const LOL_MAPID_PREFIX = "mapId";
    const LOL_GAMEMODE_MODE = "CLASSIC";
    const LOL_GAMETYPE_TYPE = "CUSTOM_GAME";
    const LOL_MAPID_MAPID = "1";

	private $esportid;
	private $playerid;
	private $region;

	public function __construct($params)
    {

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
    			return "No eSport corresponding to ID ".$this->esportid." found";
    			break;
    	}
    }

    private function _update_lol()
    {
    	$CI =& get_instance();
        $CI->load->library('lol_api');
        $recent_matches = $CI->lol_api->get_recent_matches($this->playerid);

        foreach ($recent_matches as $recent_match)
        {
            if($recent_match[self::LOL_GAMETYPE_PREFIX] = self::LOL_GAMETYPE_TYPE
                && $recent_match[self::LOL_GAMEMODE_PREFIX] = self::LOL_GAMEMODE_MODE
                && $recent_match[self::LOL_MAPID_PREFIX] = self::LOL_MAPID_MAPID)
            {
                //Match is valid, check team composition
                
            }
        }

    	return $matches;
    }
}