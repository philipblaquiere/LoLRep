<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_validator
{
    
    const LOL_TEAM_SIZE = 9;
    const LOL_PLAYERID_PREFIX = "summonerId";
    const LOL_PLAYERID = "playerid";
    const LOL_GAMEID_PREFIX = "gameId";
    const LOL_GAMEID = "matchid";
    const LOL_GAMES_PREFIX = "games";
    const LOL_GAMEDATE_PREFIX = "createDate";
    const LOL_GAMEDATE = "match_date";
    const LOL_GAMETYPE_PREFIX = "gameType";
    const LOL_GAMEMODE_PREFIX = "gameMode";
    const LOL_MAPID_PREFIX = "mapId";
    const LOL_GAMEMODE_MODE = "CLASSIC";
    const LOL_GAMETYPE_TYPE = "CUSTOM_GAME";
    const LOL_MAPID_MAPID = "1";
    const LOL_FELLOWPLAYERS_PREFIX = "fellowPlayers";
    const LOL_TEAMID_PREFIX = "teamId";
    const LOL_CHAMPION_PREFIX = "championId";
    const LOL_TEAMA = "100";
    const LOL_TEAMB = "200";

    const LOL_ERROR_TEAMSIZE = "Incorrect team size";
    const LOL_ERROR_GAMEDATE_LATE = "Game created a later time than scheduled";
    const LOL_ERROR_GAMEDATE_EARLY = "Game created earlier than the scheduled time";
    const LOL_ERROR_GAMETYPE = "Game type does not match a valid game type";
    const LOL_ERROR_GAMEMODE = "Game mode does not match a valid game mode";
    const LOL_ERROR_MAP = "Game map does not match a valid game map";
    const LOL_ERROR_TEAMA_OK = "Team A's team is complete, but Team B is not complete";
    const LOL_ERROR_TEAMB_OK = "Team B's team is complete, but Team A is not complete";

    const MATCH_CREATE_TIME_LEEWAY = "1800"; //1800 seconds = 30 minutes
    const MATCH_DETAILS = "match_details";
    const MATCH_INFO_TEAMA_COMPLETE = "teama_complete";
    const MATCH_INFO_TEAMB_COMPLETE = "teamb_complete";
    const MATCH_INFO_ERROR_MSG = "error_message";
    const MATCH_INFO_ERROR_META = "error_meta";
    const MATCH_INFO_TIMESTAMP = "checked_timestamp";
    const MATCH_PLAYERS_TEAMA = "teama_players";
    const MATCH_PLAYERS_TEAMB = "teamb_players";
    const INVALID_MATCHES = "invalid_matches";
    const VALID_MATCHES = "valid_matches";

	public function __construct()
	{
    }

    public function validate($match, $recent_games, $include_invalid_results, $esportid)
    {
        if($match)
        {
            switch ($esportid) {
                case '1':
                    return $this->_validate_lol($match, $recent_games, $include_invalid_results);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        else
        {
            return FALSE;
        }
        
    }
//1475919225
    /*
    *   Compares an array of recent games pulled from the LoL API to a scheduled match, 
    *   returns the corresponding valid LoL match details, if found
    */
    private function _validate_lol($match, $recent_games, $include_invalid_results)
    {
        //League of Legends
        $match_infos[self::VALID_MATCHES] = array();
        if($include_invalid_results)
        {
            $match_infos[self::INVALID_MATCHES] = array();
        }
        $teama_player_info = $match['teama']['roster'];
        $teamb_player_info = $match['teamb']['roster'];
         
        $teama_roster = array();
        $teamb_roster = array();

        
        foreach ($teama_player_info as &$teama_player)
        {
            array_push($teama_roster, $teama_player[self::LOL_PLAYERID]);
        }
        foreach ($teamb_player_info as &$teamb_player)
        {
            array_push($teamb_roster, $teamb_player[self::LOL_PLAYERID]);
        }

        $current_playerid = $recent_games[self::LOL_PLAYERID_PREFIX];
        
        foreach ($recent_games[self::LOL_GAMES_PREFIX] as $game)
        {
            $teama_players = array();
            $teamb_players = array();
            $match_info[self::LOL_GAMEID_PREFIX] = $game[self::LOL_GAMEID_PREFIX];
            $match_info[self::LOL_PLAYERID_PREFIX] = $current_playerid;
            $match_info[self::LOL_GAMEID] = $match[self::LOL_GAMEID];
            $match_info[self::MATCH_INFO_TEAMA_COMPLETE] = 0;
            $match_info[self::MATCH_INFO_TEAMB_COMPLETE] = 0;
            $match_info[self::MATCH_INFO_TIMESTAMP] = time();
            $match_info[self::MATCH_INFO_ERROR_MSG] = "";
            $match_info[self::MATCH_INFO_ERROR_META] = "";
            $match_info[self::MATCH_DETAILS] = "";

            if($this->_remove_last_three_digits($game[self::LOL_GAMEDATE_PREFIX]) <= ($match[self::LOL_GAMEDATE] + self::MATCH_CREATE_TIME_LEEWAY))
            {
                if($this->_remove_last_three_digits($game[self::LOL_GAMEDATE_PREFIX]) >= ($match[self::LOL_GAMEDATE] - self::MATCH_CREATE_TIME_LEEWAY))
                {
                    /*if(count($game[self::LOL_FELLOWPLAYERS_PREFIX]) != self::LOL_TEAM_SIZE)
                    {*/
                        if($game[self::LOL_GAMETYPE_PREFIX] == self::LOL_GAMETYPE_TYPE)
                        {
                            if($game[self::LOL_GAMEMODE_PREFIX] == self::LOL_GAMEMODE_MODE)
                            {
                                if($game[self::LOL_MAPID_PREFIX] == self::LOL_MAPID_MAPID)
                                {
                                    //Match is valid, check team composition
                                    //LoL Match has occured after the scheduled time
                                    $unmatcheda_players = 0;
                                    $unmatchedb_players = 0;
                                    $fellow_players = $game[self::LOL_FELLOWPLAYERS_PREFIX];
                                    $teams[self::LOL_TEAMA] = array();
                                    $teams[self::LOL_TEAMB] = array();

                                    foreach ($fellow_players as $fellow_player)
                                    {
                                        $teams[$fellow_player[self::LOL_TEAMID_PREFIX]][$fellow_player[self::LOL_PLAYERID_PREFIX]] = $fellow_player;
                                    }
                                    $current_player[self::LOL_TEAMID_PREFIX] = $game[self::LOL_TEAMID_PREFIX];
                                    $current_player[self::LOL_PLAYERID_PREFIX] = $current_playerid;
                                    $current_player[self::LOL_CHAMPION_PREFIX] = $game[self::LOL_CHAMPION_PREFIX];
                                    $teams[$current_player[self::LOL_TEAMID_PREFIX]][$current_player[self::LOL_PLAYERID_PREFIX]] = $current_player;

                                    foreach ($teams[self::LOL_TEAMA] as $teama_fellow_player)
                                    {
                                        if(!in_array($teama_fellow_player[self::LOL_PLAYERID_PREFIX], $teama_roster))
                                        {
                                            $unmatcheda_players += 1;
                                            continue;
                                        }
                                        else
                                        {
                                            $teama_players[$teama_fellow_player[self::LOL_PLAYERID_PREFIX]] = $teama_fellow_player[self::LOL_PLAYERID_PREFIX];
                                        }
                                    }
                                    if ($unmatcheda_players == 0)
                                    {
                                        $match_info[self::MATCH_INFO_TEAMA_COMPLETE] = 1;

                                        foreach ($teams[self::LOL_TEAMB] as $teamb_fellow_player)
                                        {
                                            if(!in_array($teamb_fellow_player[self::LOL_PLAYERID_PREFIX], $teamb_roster))
                                            {
                                                $unmatchedb_players +=1;
                                                continue;
                                            }
                                            else
                                            {
                                                $teamb_players[$teamb_fellow_player[self::LOL_PLAYERID_PREFIX]] = $teamb_fellow_player[self::LOL_PLAYERID_PREFIX];
                                            }
                                        }
                                        if($unmatchedb_players == 0)
                                        {
                                            $match_info[self::MATCH_INFO_TEAMB_COMPLETE] = 1;
                                        }
                                    }

                                    if($match_info[self::MATCH_INFO_TEAMA_COMPLETE] && $match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                    {
                                        $match_info[self::MATCH_DETAILS] = $game;
                                        $match_info[self::LOL_TEAMA] = $teams[self::LOL_TEAMA];
                                        $match_info[self::LOL_TEAMB] = $teams[self::LOL_TEAMB];
                                        array_push($match_infos[self::VALID_MATCHES], $match_info);
                                        continue;
                                        //no need to iterate more match is found;
                                    }
                                    elseif ($match_info[self::MATCH_INFO_TEAMA_COMPLETE] && !$match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                    {
                                        $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_TEAMA_OK;
                                    }
                                    elseif (!$match_info[self::MATCH_INFO_TEAMA_COMPLETE] && $match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                    {
                                        $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_TEAMB_OK;
                                    }
                                    else
                                    {
                                        //check the case where teama fellow players could be teamb.
                                        $unmatcheda_players = 0;
                                        $unmatchedb_players = 0;
                                        $teama_players = array();
                                        $teamb_players = array();
                                        foreach ($teams[self::LOL_TEAMA] as $teama_fellow_player)
                                        {
                                            if(!in_array($teama_fellow_player[self::LOL_PLAYERID_PREFIX], $teamb_roster))
                                            {
                                                $unmatchedb_players +=1;
                                                continue;
                                            }
                                            else
                                            {
                                                $teamb_players[$teama_fellow_player[self::LOL_PLAYERID_PREFIX]] = $teama_fellow_player[self::LOL_PLAYERID_PREFIX];
                                            }
                                        }
                                        if ($unmatchedb_players == 0)
                                        {
                                            $match_info[self::MATCH_INFO_TEAMB_COMPLETE] = 1;
                                            foreach ($teams[self::LOL_TEAMB] as $teamb_fellow_player)
                                            {
                                                if(!in_array($teamb_fellow_player[self::LOL_PLAYERID_PREFIX], $teama_roster))
                                                {
                                                    $unmatcheda_players +=1;
                                                    continue;
                                                }
                                                else
                                                {
                                                    $teama_players[$teamb_fellow_player[self::LOL_PLAYERID_PREFIX]] = $teamb_fellow_player[self::LOL_PLAYERID_PREFIX];
                                                }
                                            }
                                        }

                                        
                                        if ($unmatcheda_players == 0)
                                        {
                                             $match_info[self::MATCH_INFO_TEAMA_COMPLETE] = 1;
                                        }

                                        if($match_info[self::MATCH_INFO_TEAMA_COMPLETE] && $match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                        {
                                            $match_info[self::MATCH_DETAILS] = $game;
                                            $match_info[self::LOL_TEAMA] = $teams[self::LOL_TEAMA];
                                            $match_info[self::LOL_TEAMB] = $teams[self::LOL_TEAMB];
                                            array_push($match_infos[self::VALID_MATCHES], $match_info);
                                            continue;
                                            //no need to iterate more match is found;
                                        }
                                        elseif ($match_info[self::MATCH_INFO_TEAMA_COMPLETE] && !$match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                        {
                                            $match_info[self::MATCH_INFO_ERROR_MSG] = self::ERROR_TEAMA_OK;

                                        }
                                        elseif (!$match_info[self::MATCH_INFO_TEAMA_COMPLETE] && $match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                        {
                                            $match_info[self::MATCH_INFO_ERROR_MSG] = self::ERROR_TEAMB_OK;
                                        }
                                        else
                                        {
                                            
                                        }
                                    }
                                }
                                else
                                {
                                    $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_MAP;
                                    $match_info[self::MATCH_INFO_ERROR_META] = "Game map is currently" . $game[self::LOL_MAPID_PREFIX] . " should be " . self::LOL_MAPID_MAPID;
                                }
                            }
                            else
                            {
                                $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_GAMEMODE;
                                $match_info[self::MATCH_INFO_ERROR_META] = "Game mode is currently" . $game[self::LOL_GAMEMODE_PREFIX] . " should be " . self::LOL_GAMEMODE_MODE;
                            }
                        /*}
                        else
                        {
                            $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_TEAMSIZE;
                            $match_info[self::MATCH_INFO_ERROR_META] = "Team size is was " . (count($game[self::LOL_FELLOWPLAYERS_PREFIX]) + 1) . " should be " . self::LOL_GAMETYPE_TYPE;
                        }*/
                    }
                    else
                    {
                        $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_GAMETYPE;
                        $match_info[self::MATCH_INFO_ERROR_META] = "Game type is currently " . $game[self::LOL_GAMETYPE_PREFIX] . " should be " . self::LOL_GAMETYPE_TYPE;
                    }  
                }
                else
                {
                    $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_GAMEDATE_EARLY;
                    $match_info[self::MATCH_INFO_ERROR_META] =  $this->_remove_last_three_digits($game[self::LOL_GAMEDATE_PREFIX]) . " scheduled : ". $match[self::LOL_GAMEDATE];
                }
            }
            else
            {
                $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_GAMEDATE_LATE;
                $match_info[self::MATCH_INFO_ERROR_META] = $this->_remove_last_three_digits($game[self::LOL_GAMEDATE_PREFIX]) . " scheduled : ". $match[self::LOL_GAMEDATE];
            }
            
                
            if($include_invalid_results)
            {
                array_push($match_infos[self::INVALID_MATCHES], $match_info);
            }
        }
        
        return $match_infos;
    }

    private function _remove_last_three_digits($time)
    {
        return intval($time/1000);
    }


}
