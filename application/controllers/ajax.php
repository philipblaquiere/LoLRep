<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller 
{
	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('user_model');
	    $this->load->model('system_message_model');
	    $this->load->model('banned_model');
		$this->load->model('player_model');
		$this->load->library('lol_api');
		$this->load->model('team_model');
		$this->load->library('league_cache');
		$this->load->model('statistics_model');
		$this->load->library('stats_formatter');
	}

	public function authenticate_summoner($region, $summonerinput)
	{
		$region = urldecode($region);
      	$region = 'na';
	    if($summonerinput== "-")
	    {
	       //user didn't enter anything, show error message and reload.
		    $data['errormessage'] = "You must enter a summoner name to validate.";
			  $this->load->view('messages/rune_page_verification_fail', $data);
			  return;
	    }
	    else if($region == "Region")
	    {
	    	$data['errormessage'] = "You must select a region";
			  $this->load->view('messages/rune_page_verification_fail', $data);
			  return;
	    }
	    else
	    {
  			//check riot servers to see if summoner actually exists.
  			$summonerinput = strtolower(str_replace(' ','', urldecode($summonerinput)));
  			$riotsummoners = $this->lol_api->getSummonerByName($summonerinput);
  			$riotsummoners['region'] = "na";
  			//contains Array ( [summonername] => Array ( [id] => 39895516 [name] => Summoner Name [profileIconId] => 0 [summonerLevel] => 6 [revisionDate] => 1383423931000 ) [region] => Region )
			if(!array_key_exists($summonerinput, $riotsummoners))
			{
				//data['errormessage'] = "Error " . $riotsummoners['status']['status_code'] . " : " . $riotsummoners['status']['message'];
        $data['errormessage'] = implode(" ", $riotsummoners);
				$this->load->view('messages/rune_page_verification_fail', $data);
				return;
			}
			else
			{
				//check to see if summoner is banned
				//$banned_summoner = $this->banned_model->get_bysummonername($riotsummoners[$summonerinput]['name']);
				//summoner exists, check if player exists already in db
				$player = $this->player_model->get_player_by_name($riotsummoners[$summonerinput]['name'], $this->get_esportid());
        /*if($banned_summoner) 
				{
					$data['errormessage'] = "The specified summoner has been banned from our website";
					$this->load->view('messages/rune_page_verification_fail', $data);
					return;
				}*/
				if(empty($player))
				{
					//player doesn't exist in db yet. Generate a Rune Page Key
					$_SESSION['runepagekey'] = $this->user_model->generate_rune_page_key();
					$data['runepagekey'] = $_SESSION['runepagekey'];
					$_SESSION['player']['player_name'] = $riotsummoners[$summonerinput]['name'];
					$_SESSION['player']['playerid'] = $riotsummoners[$summonerinput]['id'];
					$_SESSION['player']['icon'] = $riotsummoners[$summonerinput]['profileIconId'];
					$_SESSION['player']['player_name'] = $riotsummoners[$summonerinput]['name'];
					$_SESSION['player']['region'] = $riotsummoners['region'];
			  		$runepages = $this->lol_api->getSummoner($_SESSION['player']['playerid'],"runes");
					$this->load->view('ajax/authenticate_summoner',$data);
					return;
				}
				else
				{
					//summoner already existing return error
					$data['errormessage'] = "Player is already registered in our database";
					$this->load->view('messages/rune_page_verification_fail', $data);
					return;
				}
			}
		}
		//end function
  	}

	public function rune_page_verification()
	{
		$playerid = $_SESSION['player']['playerid'];
		$runepagekey = $_SESSION['runepagekey'];
		$runepages = $this->lol_api->getSummoner($playerid,"runes");
		
		$firstRunePageName = $runepages[$playerid]['pages']['0']['name'];
		if($firstRunePageName == $runepagekey)
		{
			//user runepage is validated, re-check absence in db
			$player = $this->player_model->get_player_by_name($playerid, $this->get_esportid());
			if(empty($player))
			{
				echo "success";
			}
			else 
			{
				//user was registered during verification phase (highly unlikely), display error
      	$data['errormessage'] = "The specified summoner is already registered";
			  $this->load->view('messages/rune_page_verification_fail', $data);
			}
		}
		else
		{
			//user is invalid, display error message.
			$data['errormessage'] = "Incorrect Rune page name (" . $firstRunePageName . "), should be " . $runepagekey;
			$this->load->view('messages/rune_page_verification_fail', $data);
		}
	}

	private function _get_player_seasonid($player)
	{
		$seasonids = array();
		foreach ($player['teams'] as $teamid)
		{
			if(isset($player['teams_meta'][$teamid]['current_season']))
			{
				array_push($seasonids, $player['teams_meta'][$teamid]['current_season']);
			}
		}
		return $seasonids;
	}

	private function _get_team_seasonid($team)
	{
		if(isset($team['leagues']) && isset($team['leagues']['current_season']))
		{
			return $team['leagues']['current_season'];
		}
		return NULL;

	}

	public function player_recent_matches($playerid)
	{
		$player = $this->player_model->get_player($playerid, $this->get_esportid());
		$seasonids = $this->_get_player_seasonid($player);
		$params = array('team' => $player['teams'], 'esportid' => $this->get_esportid(), 'playerid' => $player['playerid'], 'seasonid' => $seasonids, 'region' => $player['region']);
		$this->load->library('match_aggregator', $params);
		$matches = array_filter($this->match_aggregator->get_recent_matches());
		$data['matches'] = $matches;
		//print_r($matches);
		$prefix = $this->get_esport_prefix();
		if($prefix == "")
		{
		  return NULL;
		}
		$view = "recent_matches_".$prefix;
		$this->load->view($view, $data);
	}

	public function player_upcoming_matches($playerid)
	{
		$player = $this->player_model->get_player($playerid, $this->get_esportid());
		$params = array('teamids' => $player['teams'], 'esportid' => $this->get_esportid(), 'playerid' => $player['playerid']);
		$this->load->library('match_aggregator', $params);
		$matches = array_filter($this->match_aggregator->get_upcoming_matches());
		$data['matches'] = $matches;
		$prefix = $this->get_esport_prefix();
		if($prefix == "")
		{
		  return NULL;
		}
		$view = "upcoming_matches_".$prefix;
		$this->load->view($view, $data);
	}

	public function player_stats($playerid)
	{
		$player = $this->player_model->get_player($playerid, $this->get_esportid());
		$data['player'] = $player;
		$data['stats'] = NULL;
		if(isset($player['teams']) && isset($player['teams_meta']) && isset($player['teams_meta'][$player['teams'][0]]['current_season']))
		{
			$data['current_team'] = $player['teams'][0];
			$data['current_season'] = $player['teams_meta'][$player['teams'][0]]['current_season'];
			$data['current_league'] = $player['teams_meta'][$player['teams'][0]]['current_league'];
			$team_stats = $this->statistics_model->get_team_stats($data['current_team'], $data['current_league'],$data['current_season'], $this->get_esportid());
			$player_stats = isset($team_stats['player_stats'][$playerid]) ? $team_stats['player_stats'][$playerid] : NULL;
			if($player_stats != NULL)
			{
				$player_stats = $this->stats_formatter->calculate_averages($player_stats, $this->get_esportid());
			}
			$data['stats'] = $player_stats;
		}
		$view = "stats";
		$this->load->view($view, $data);
	}

	public function team_recent_matches($teamid)
	{
		$matches = array();
		$team = $this->team_model->get_team($teamid);
		$seasonid = $this->_get_team_seasonid($team);

		if($seasonid != NULL)
		{
			$params = array('team' => $team, 'esportid' => $this->get_esportid(), 'seasonid' => $seasonid);
			$this->load->library('match_aggregator', $params);
			$matches = array_filter($this->match_aggregator->get_recent_matches());
		}
		$data['matches'] = $matches;
		//print_r($matches);
		$prefix = $this->get_esport_prefix();
		if($prefix == "")
		{
		  return NULL;
		}
		$view = "recent_matches_".$prefix;
		$this->load->view($view, $data);
	}

	public function team_upcoming_matches($teamid)
	{
		$matches = array();
		$team = $this->team_model->get_team($teamid);
		$seasonid = $this->_get_team_seasonid($team);

		if($seasonid != NULL)
		{
			$params = array('team' => $team, 'esportid' => $this->get_esportid(), 'seasonid' => $seasonid);
			$this->load->library('match_aggregator', $params);
			$matches = array_filter($this->match_aggregator->get_upcoming_matches());
		}
		$data['matches'] = $matches;
		//print_r($matches);
		$prefix = $this->get_esport_prefix();
		if($prefix == "")
		{
		  return NULL;
		}
		$view = "upcoming_matches_".$prefix;
		$this->load->view($view, $data);
	}

	public function team_roster($teamid)
	{
		$team = $this->team_model->get_team($teamid, $this->get_esportid());
		$data['team'] = $team;
		$this->load->view('team_roster',$data);
	}

	public function team_stats($teamid)
	{
		$data['stats'] = NULL;
		$team = $this->team_model->get_team($teamid, $this->get_esportid());
		if(isset($team['leagues']) && isset($team['leagues']['current_season']))
		{
			$data['current_team'] = $team['teamid'];
			$data['current_season'] = $team['leagues']['current_season'];
			$data['current_league'] = $team['leagues']['current_league'];
			$team_stats = $this->statistics_model->get_team_stats($data['current_team'], $data['current_league'],$data['current_season'], $this->get_esportid());
			$team_stats = isset($team_stats['player_stats']) ? $team_stats['player_stats'] : NULL;
			if($team_stats != NULL)
			{
				$team_stats = $this->stats_formatter->calculate_averages($team_stats, $this->get_esportid());
			}
			$data['stats'] = $team_stats;
		}
		$prefix = $this->get_esport_prefix();
		if($prefix == "")
		{
		  return NULL;
		}
		$view = "stats";
		$this->load->view($view, $data);
	}

	public function search_leagues()
	{
		$params = array('league_not_full' => $_POST['notfull'],
						'league_not_empty' => $_POST['notempty'],
						'invite' => $_POST['inviteonly'],
						'search_text' => $_POST['searchtext']);
		$data['leagues'] = $this->league_cache->search($params);
		$this->load->view('league_list', $data);
	}

}	
