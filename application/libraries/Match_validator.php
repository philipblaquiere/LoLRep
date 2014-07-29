<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_validator
{
    
    const LOL_PLAYERID_PREFIX = "summonerId";
    const LOL_PLAYERID = "playerid";
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
    const LOL_FELLOWPLAYERS_PREFIX = "fellowPlayers";
    const LOL_TEAMID_PREFIX = "teamId";
    const LOL_TEAMAID = "100";
    const LOL_TEAMBID = "200";

    const LOL_ERROR_GAMEDATE = "Game created a later time than scheduled";
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
    const INVALID_MATCHES = "invalid_matches";
    const VALID_MATCHES = "valid_matches";

	public function __construct()
	{
    }

    public function validate($match, $recent_games, $esportid)
    {
        if($match)
        {
            switch ($esportid) {
                case '1':
                    return $this->_validate_lol($match, $recent_games);
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

    /*
    *   Compares an array of recent games pulled from the LoL API to a scheduled match, 
    *   returns the corresponding valid LoL match details, if found
    */
    private function _validate_lol($match, $recent_games)
    {
        //League of Legends
        $match_infos[self::VALID_MATCHES] = array();
        $match_infos[self::INVALID_MATCHES] = array();
        $teama_roster = $match['teama']['roster'];
        $teamb_roster = $match['teamb']['roster'];
         
        $teama_players = array();
        $teamb_players = array();
        foreach ($teama_roster as &$teama_player)
        {
            array_push($teama_players, $teama_player[self::LOL_PLAYERID]);
        }
        foreach ($teamb_roster as &$teamb_player)
        {
            array_push($teamb_players, $teamb_player[self::LOL_PLAYERID]);
        }

        foreach ($recent_games['games'] as $game)
        {
            $match_info[self::LOL_GAMEID] = $game[self::LOL_GAMEID_PREFIX];
            $match_info[self::MATCH_INFO_TEAMA_COMPLETE] = 0;
            $match_info[self::MATCH_INFO_TEAMB_COMPLETE] = 0;
            $match_info[self::MATCH_INFO_TIMESTAMP] = time();
            $match_info[self::MATCH_INFO_ERROR_MSG] = "";
            $match_info[self::MATCH_INFO_ERROR_META] = "";
            $match_info[self::MATCH_DETAILS] = "";

            if($this->_remove_last_three_digits($game[self::LOL_GAMEDATE_PREFIX]) <= 
                ($match[self::LOL_GAMEDATE] + self::MATCH_CREATE_TIME_LEEWAY))
            {
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
                            $teama_fellow_players = array();
                            $teamb_fellow_players = array();

                            foreach ($fellow_players as $fellow_player) {
                                if($fellow_player[self::LOL_TEAMID_PREFIX] == self::LOL_TEAMAID)
                                {
                                    array_push($teama_fellow_players, $fellow_player);
                                }
                                else
                                {
                                    array_push($teamb_fellow_players, $fellow_player);
                                }
                            }

                            foreach ($teama_fellow_players as $teama_fellow_player)
                            {
                                if(!in_array($teama_fellow_player[self::LOL_PLAYERID_PREFIX], $teama_players))
                                {
                                    $unmatcheda_players += 1;
                                }
                            }
                            if ($unmatcheda_players == 0)
                            {
                                $match_info[self::MATCH_INFO_TEAMA_COMPLETE] = 1;
                            }

                            foreach ($teamb_fellow_players as $teamb_fellow_player)
                            {
                                if(!in_array($teamb_fellow_player[self::LOL_PLAYERID_PREFIX], $teamb_players))
                                {
                                    $unmatchedb_players +=1;
                                }
                            }

                            if($unmatchedb_players == 0)
                            {
                                $match_info[self::MATCH_INFO_TEAMB_COMPLETE] = 1;
                            }

                            if($match_info[self::MATCH_INFO_TEAMA_COMPLETE] && $match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                            {
                                $match_info[self::MATCH_DETAILS] = $game;
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
                                foreach ($teama_fellow_players as $teama_fellow_player)
                                {
                                    if(!in_array($teama_fellow_player[self::LOL_PLAYERID_PREFIX], $teamb_players))
                                    {
                                        $unmatchedb_players +=1;
                                    }
                                }
                                if ($unmatchedb_players == 0)
                                {
                                     $match_info[self::MATCH_INFO_TEAMB_COMPLETE] = 1;
                                }

                                foreach ($teamb_fellow_players as $teamb_fellow_player)
                                {
                                    if(!in_array($teamb_fellow_player[self::LOL_PLAYERID_PREFIX], $teama_players))
                                    {
                                        $unmatcheda_players +=1;
                                    }
                                }
                                if ($unmatcheda_players == 0)
                                {
                                     $match_info[self::MATCH_INFO_TEAMA_COMPLETE] = 1;
                                }

                                if($match_info[self::MATCH_INFO_TEAMA_COMPLETE] && $match_info[self::MATCH_INFO_TEAMB_COMPLETE])
                                {
                                    $match_info[self::MATCH_DETAILS] = $game;
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
                }
                else
                {
                    $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_GAMETYPE;
                    $match_info[self::MATCH_INFO_ERROR_META] = "Game type is currently " . $game[self::LOL_GAMETYPE_PREFIX] . " should be " . self::LOL_GAMETYPE_TYPE;
                }
            }
            else
            {
                $match_info[self::MATCH_INFO_ERROR_MSG] = self::LOL_ERROR_GAMEDATE;
                $match_info[self::MATCH_INFO_ERROR_META] = $this->_remove_last_three_digits($game[self::LOL_GAMEDATE_PREFIX]) . " scheduled : ". $match[self::LOL_GAMEDATE];
            }
            array_push($match_infos[self::INVALID_MATCHES], $match_info);
        }
        return $match_infos;
    }

    private function _remove_last_three_digits($time)
    {
        return intval($time/1000);
    }


}
