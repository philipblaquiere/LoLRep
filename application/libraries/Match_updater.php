<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_updater
{
    const LOL_PLAYERID = "playerid";
    const MSG_NO_SCHEDULED_MATCHES = "No scheduled matches found";
    const ERROR_LOL_API = "The League of Legends API isn't responding";
    const ERROR_ESPORTID = "No corresponding eSport has been found";

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
        $this->CI->load->library('match_formatter');
        $this->CI->match_formatter->set_esportid($this->esportid);
        
        $params = array(self::LOL_PLAYERID => $this->playerid);
        $this->CI->load->library('match_cache',$params);
        $this->CI->load->library('match_validator', $params);
    }

    public function update($include_invalid_results = FALSE)
    {
        $scheduled_matchids = $this->_get_scheduled_matches();
        if($scheduled_matchids)
        {
            $scheduled_matches = $this->CI->match_model->get_matches($scheduled_matchids, $this->esportid);

            switch ($this->esportid)
            {
                case '1':
                    return $this->_update_lol($scheduled_matches, $include_invalid_results);
                    break; 
                default:
                    $response['error'] = self::ERROR_ESPORTID;
                    return $response;
            }
        }
        else
        {
            $response['error'] = self::MSG_NO_SCHEDULED_MATCHES;
            return $response;
        }
    }

    /*
    |    Gets the matchids of matches that are scheduled, 
    |    have not yet been updated in the db,
    |    and might have been played
    */
    private function _get_scheduled_matches()
    {
        $scheduled_matchids = $this->CI->match_model->get_scheduled_matches($this->teamids, time());
        return $scheduled_matchids;
    }

    private function _update_lol($scheduled_matches, $include_invalid_results)
    {   
        $match_results = array();
        $recent_lol_matches = array();
        $formatted_match = array();
        foreach ($scheduled_matches as $scheduled_match)
        {
            if(!$this->CI->match_cache->has_match($scheduled_match['matchid']))
            {
                if(empty($recent_lol_matches))
                {
                    $recent_lol_matches = $this->CI->lol_api->get_recent_matches($this->playerid);
                }

                $match_result = $this->CI->match_validator->validate($scheduled_match, $recent_lol_matches, $include_invalid_results, $this->esportid);
                
                if(count($match_result['valid_matches']) >= 1)
                {
                    $match_results[$scheduled_match['matchid']] = $match_result;
                    $scheduled_match['gameid'] = $match_result['valid_matches'][0]['match_details']['gameId'];
                    $match_results[$scheduled_match['matchid']]['match_info'] = $scheduled_match;

                    $formatted_match = $this->_format_match($match_results[$scheduled_match['matchid']], null);
                    //Update Match Cache with a new, data incomplete match
                    $this->_add_match_to_cache($formatted_match);
                    $team_players = $this->_get_unified_team_array($formatted_match);
                    
                    foreach ($team_players as $team_player)
                    {
                        $recent_lol_matches = $this->CI->lol_api->get_recent_matches($team_player['summonerId']);
                        if($recent_lol_matches)
                        {
                            foreach ($recent_lol_matches['games'] as $game) 
                            {
                                $game['summonerId'] = $recent_lol_matches['summonerId'];
                                if($game['gameId'] == $formatted_match['gameid'])
                                {
                                    $formatted_match = $this->_format_match($formatted_match, $game);
                                    continue;
                                }
                            }
                        }
                        else
                        {
                            return "Unable to process request, LoL API not working";
                        }
                    }
                    $formatted_match = $this->CI->match_formatter->update_winner($formatted_match);
                    $formatted_match['complete'] = TRUE;
                    $this->_add_match_to_cache($formatted_match);

                }

            }
            else
            {
                return $this->CI->match_cache->get_match($scheduled_match['matchid']);
            }
        }
        return $formatted_match;
    }

    private function _format_match($match, $game)
    {
        return $this->CI->match_formatter->format($match, $game);
    }

    private function _add_match_to_cache($match)
    {
        $this->CI->match_cache->add_match($match);
    }

    private function _get_unified_team_array($match)
    {
        $teama_players = $match['teama']['teama_players'];
        $teamb_players = $match['teamb']['teamb_players'];
        $team_players = array_replace($teama_players, $teamb_players);
        unset($team_players[$this->playerid]);
        return $team_players;
    }
}