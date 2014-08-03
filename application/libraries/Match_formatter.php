<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_formatter
{

	const LOL_PLAYERID = "summonerId";
	const LOL_CHAMPIONID = "championId";
	const LOL_SPELL1 = "spell1";
	const LOL_SPELL2 = "spell2";
	const LOL_LEVEL = "level";
	const LOL_STATS = "stats";
    const LOL_TEAM_100 = "100";
    const LOL_TEAM_200 = "200";

	const VALID_MATCHES_KEY = "valid_matches";
	const MATCH_INFO_KEY = "match_info";
	const MATCH_DETAILS_KEY = "match_details";
	const MATCH_TEAMA_PLAYERS = "teama_players";
	const MATCH_TEAMB_PLAYERS = "teamb_players";
    const MATCH_TEAMA_ID = "teamaid";
    const MATCH_TEAMB_ID = "teambid";
	const MATCH_TEAMA = "teama";
	const MATCH_TEAMB = "teamb";
    const MATCH_COMPLETE = "complete";
    const MATCH_WINNER_ID = "winnerid";
    const MATCH_PLAYERID = "playerid";

    const TEAM_ROSTER = "roster";

	private $esportid;

	public function __construct()
    {
    	
    }
    public function set_esportid($esportid)
    {
    	$this->esportid = $esportid;
    }

    public function format($match, $game)
    {
    	switch ($this->esportid) {
    		case '1':
    			return $this->_format_lol($match, $game);
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function _format_lol($match, $game)
    {

    	if(array_key_exists(self::VALID_MATCHES_KEY, $match))
    	{
    		$playerid = $match[self::VALID_MATCHES_KEY][0][self::LOL_PLAYERID];
    		$game = $match[self::VALID_MATCHES_KEY][0][self::MATCH_DETAILS_KEY];
    		$match_info = $match[self::MATCH_INFO_KEY];
    		$stats = $match[self::VALID_MATCHES_KEY][0][self::MATCH_DETAILS_KEY][self::LOL_STATS];
    		$teama = $match[self::VALID_MATCHES_KEY][0][self::LOL_TEAM_100];
    		$teamb = $match[self::VALID_MATCHES_KEY][0][self::LOL_TEAM_200];
    		if(array_key_exists($playerid, $teama))
    		{
    			$teama[$playerid][self::LOL_CHAMPIONID] = $game[self::LOL_CHAMPIONID];
    			$teama[$playerid][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
    			$teama[$playerid][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
    			$teama[$playerid][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
    			$teama[$playerid][self::LOL_STATS] = $stats;
    		}
    		else
  			{
  				$teamb[$playerid][self::LOL_CHAMPIONID] = $game[self::LOL_CHAMPIONID];
    			$teamb[$playerid][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
    			$teamb[$playerid][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
    			$teamb[$playerid][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
    			$teamb[$playerid][self::LOL_STATS] = $stats;
  			}

            $player_in_teama = FALSE;

            foreach ($match_info[self::MATCH_TEAMA][self::TEAM_ROSTER] as $player)
            {
                if($player[self::MATCH_PLAYERID] == $playerid)
                {
                    $player_in_teama = TRUE;
                    continue;
                }
            }
            if($player_in_teama)
            {
                $match_info[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS] = $teama;
                $match_info[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS] = $teamb;
            }
            else
            {
                $match_info[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS] = $teamb;
                $match_info[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS] = $teama;
            }
  			
            //Signify that the match information is not complete, missing player stats
            $match_info[self::MATCH_COMPLETE] = FALSE;    	
        }
    	else
    	{
    		//Match is already formatted, add player stats to the player list
    		if(array_key_exists($game[self::LOL_PLAYERID], $match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS]))
    		{
    			//Player is in teama
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_STATS] = $game[self::LOL_STATS];
    		}
    		else
    		{
    			//Player is in teamb
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_STATS] = $game[self::LOL_STATS];
    		}
            $match_info = $match;
    	}
    	return $match_info;
    }

    public function update_winner($match)
    {
        $teamaid = $match[self::MATCH_TEAMA][self::MATCH_TEAMA_ID];
        $teambid = $match[self::MATCH_TEAMB][self::MATCH_TEAMB_ID];
        foreach ($match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS] as $player)
        {
            if(array_key_exists(self::LOL_STATS, $player) && array_key_exists('win', $player[self::LOL_STATS]))
            {
                if($player[self::LOL_STATS]['win'] == 1)
                {
                    $match[self::MATCH_WINNER_ID] = $teamaid;
                }
                else
                {
                    $match[self::MATCH_WINNER_ID] = $match[self::MATCH_TEAMB][self::MATCH_TEAMB_ID];
                }
                return $match;
            }
        }
        foreach ($match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS] as $player)
        {
            if(array_key_exists(self::LOL_STATS, $player) && array_key_exists('win', $player[self::LOL_STATS]))
            {
                if($player[self::LOL_STATS]['win'] == 1)
                {
                    $match[self::MATCH_WINNER_ID] = $teambid;
                }
                else
                {
                    $match[self::MATCH_WINNER_ID] = $match[self::MATCH_TEAMA][self::MATCH_TEAMA_ID];
                }
                return $match;
            }
        }
    }
}
