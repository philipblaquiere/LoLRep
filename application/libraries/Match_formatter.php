<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_formatter
{

	const LOL_PLAYERID = "summonerId";
	const LOL_CHAMPIONID = "championId";
    const LOL_CHAMPION_NAME = "champion_name";
    const LOL_CHAMPION_ICON = "champion_icon";
    const LOL_SPELL1_NAME = "spell1_name";
    const LOL_SPELL1_ICON = "spell1_icon";
	const LOL_SPELL1 = "spell1";
    const LOL_SPELL2_NAME = "spell2_name";
    const LOL_SPELL2_ICON = "spell2_icon";
	const LOL_SPELL2 = "spell2";
	const LOL_LEVEL = "level";
	const LOL_STATS = "stats";
    const LOL_TEAM_100 = "100";
    const LOL_TEAM_200 = "200";
    const LOL_MINIONS_KILLED = "minionsKilled";
    const LOL_TEAMID_KEY = "teamId";

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

    private $lol_champions;
    private $lol_spells;
	private $esportid;

	public function __construct()
    {
    	$this->CI =& get_instance();
        $this->CI->load->library('lol_image_formatter');
        if($this->lol_champions == NULL)
        {
            $this->_make_lol_arrays();
        }
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
                $teama[$playerid][self::LOL_CHAMPION_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_champions[$game[self::LOL_CHAMPIONID]],'champion');
    			$teama[$playerid][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
                $teama[$playerid][self::LOL_SPELL1_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL1]],'spell');
    			$teama[$playerid][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
                $teama[$playerid][self::LOL_SPELL2_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL2]],'spell');
    			$teama[$playerid][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
    			$teama[$playerid][self::LOL_STATS] = $this->_add_missing_lol_items($stats);
    		}
    		elseif(array_key_exists($playerid, $teamb))
  			{
  				$teamb[$playerid][self::LOL_CHAMPIONID] = $game[self::LOL_CHAMPIONID];
                $teamb[$playerid][self::LOL_CHAMPION_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_champions[$game[self::LOL_CHAMPIONID]],'champion');
    			$teamb[$playerid][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
                $teamb[$playerid][self::LOL_SPELL1_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL1]],'spell');
    			$teamb[$playerid][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
                $teamb[$playerid][self::LOL_SPELL2_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL2]],'spell');
    			$teamb[$playerid][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
    			$teamb[$playerid][self::LOL_STATS] = $this->_add_missing_lol_items($stats);
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
                $championid = $match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_CHAMPIONID];
                $match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_CHAMPION_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_champions[$championid],'champion');
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
                $match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL1_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL1]],'spell');
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
                $match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL2_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL2]],'spell');
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
				$match[self::MATCH_TEAMA][self::MATCH_TEAMA_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_STATS] = $this->_add_missing_lol_items($game[self::LOL_STATS]);
    		}
    		else
    		{
    			//Player is in teamb
                $championid = $match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_CHAMPIONID];
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_CHAMPION_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_champions[$championid],'champion');
                $match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL1] = $game[self::LOL_SPELL1];
                $match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL1_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL1]],'spell');
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL2] = $game[self::LOL_SPELL2];
                $match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_SPELL2_ICON] = $this->CI->lol_image_formatter->to_image_url($this->lol_spells[$game[self::LOL_SPELL2]],'spell');
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_LEVEL] = $game[self::LOL_LEVEL];
				$match[self::MATCH_TEAMB][self::MATCH_TEAMB_PLAYERS][$game[self::LOL_PLAYERID]][self::LOL_STATS] = $this->_add_missing_lol_items($game[self::LOL_STATS]);
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

    private function _add_missing_lol_items($stats)
    {
        if(!array_key_exists(self::LOL_MINIONS_KILLED, $stats))
        {
            $stats[self::LOL_MINIONS_KILLED] = 0;
        }

        for ($i=0; $i < 7; $i++)
        { 
            $item_key = 'item'.$i;
            if(!array_key_exists($item_key, $stats))
            {
                $stats[$item_key] = 0;
            }
            else
            {
                $sprite = $stats[$item_key] . ".png";
                $img_url = $this->CI->lol_image_formatter->to_image_url($sprite,'item');
                $stats[$item_key] = $img_url;
            }
        }
        return $stats;
    }

    private function _make_lol_arrays()
    {
        $this->lol_champions = array(
                '1' => "Annie.png",
                '2' => "Olaf.png",
                '3' => "Galio.png",
                '4' => "TwistedFate.png",
                '5' => "XinZhao.png",
                '6' => "Urgot.png",
                '7' => "Leblanc.png",
                '8' => "Vladimir.png",
                '9' => "FiddleSticks.png",
                '10' => "Kayle.png",
                '11' => "MasterYi.png",
                '12' => "Alistar.png",
                '13' => "Ryze.png",
                '14' => "Sion.png",
                '15' => "Sivir.png",
                '16' => "Soraka.png",
                '17' => "Teemo.png",
                '18' => "Tristana.png",
                '19' => "Warwick.png",
                '20' => "Nunu.png",
                '21' => "MissFortune.png",
                '22' => "Ashe.png",
                '23' => "Tryndamere.png",
                '24' => "Jax.png",
                '25' => "Morgana.png",
                '26' => "Zilean.png",
                '27' => "Singed.png",
                '28' => "Evelynn.png",
                '29' => "Twitch.png",
                '30' => "Karthus.png",
                '31' => "Chogath.png",
                '32' => "Amumu.png",
                '33' => "Rammus.png",
                '34' => "Anivia.png",
                '35' => "Shaco.png",
                '36' => "DrMundo.png",
                '37' => "Sona.png",
                '38' => "Kassadin.png",
                '39' => "Irelia.png",
                '40' => "Janna.png",
                '41' => "Gangplank.png",
                '42' => "Corki.png",
                '43' => "Karma.png",
                '44' => "Taric.png",
                '45' => "Veigar.png",
                '48' => "Trundle.png",
                '50' => "Swain.png",
                '51' => "Caitlyn.png",
                '53' => "Blitzcrank.png",
                '54' => "Malphite.png",
                '55' => "Katarina.png",
                '56' => "Nocturne.png",
                '57' => "Maokai.png",
                '58' => "Renekton.png",
                '59' => "JarvanIV.png",
                '60' => "Elise.png",
                '61' => "Orianna.png",
                '62' => "MonkeyKing.png",
                '63' => "Brand.png",
                '64' => "LeeSin.png",
                '67' => "Vayne.png",
                '68' => "Rumble.png",
                '69' => "Cassiopeia.png",
                '72' => "Skarner.png",
                '74' => "Heimerdinger.png",
                '75' => "Nasus.png",
                '76' => "Nidalee.png",
                '77' => "Udyr.png",
                '78' => "Poppy.png",
                '79' => "Gragas.png",
                '80' => "Pantheon.png",
                '81' => "Ezreal.png",
                '82' => "Mordekaiser.png",
                '83' => "Yorick.png",
                '84' => "Akali.png",
                '85' => "Kennen.png",
                '86' => "Garen.png",
                '89' => "Leona.png",
                '90' => "Malzahar.png",
                '91' => "Talon.png",
                '92' => "Riven.png",
                '96' => "KogMaw.png",
                '98' => "Shen.png",
                '99' => "Lux.png",
                '101' => "Xerath.png",
                '102' => "Shyvana.png",
                '103' => "Ahri.png",
                '104' => "Graves.png",
                '105' => "Fizz.png",
                '106' => "Volibear.png",
                '107' => "Rengar.png",
                '110' => "Varus.png",
                '111' => "Nautilus.png",
                '112' => "Viktor.png",
                '113' => "Sejuani.png",
                '114' => "Fiora.png",
                '115' => "Ziggs.png",
                '117' => "Lulu.png",
                '119' => "Draven.png",
                '120' => "Hecarim.png",
                '121' => "Khazix.png",
                '122' => "Darius.png",
                '126' => "Jayce.png",
                '127' => "Lissandra.png",
                '131' => "Diana.png",
                '133' => "Quinn.png",
                '134' => "Syndra.png",
                '143' => "Zyra.png",
                '154' => "Zac.png",
                '157' => "Yasuo.png",
                '161' => "Velkoz.png",
                '201' => "Braum.png",
                '222' => "Jinx.png",
                '236' => "Lucian.png",
                '238' => "Zed.png",
                '254' => "Vi.png",
                '266' => "Aatrox.png",
                '267' => "Nami.png",
                '412' => "Thresh.png");

        $this->lol_spells = array(
        '1' => "SummonerBoost.png",
        '2' => "SummonerClairvoyance.png",
        '3' => "SummonerExhaust.png",
        '4' => "SummonerFlash.png",
        '6' => "SummonerHaste.png",
        '7' => "SummonerHeal.png",
        '10' => "SummonerRevive.png",
        '11' => "SummonerSmite.png",
        '12' => "SummonerTeleport.png",
        '13' => "SummonerMana.png",
        '14' => "SummonerDot.png",
        '17' => "SummonerOdinGarrison.png",
        '21' => "SummonerBarrier.png");
    }
}
