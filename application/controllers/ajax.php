<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller 
{
	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('user_model');
	    $this->load->model('team_model');
	    $this->load->model('lol_model');
	    $this->load->model('riotapi_model');
	    $this->load->model('system_message_model');
	    $this->load->model('banned_model');
	}

	public function authenticate_summoner($region, $summonerinput)
	{
		$region = urldecode($region);
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
			$riotsummoners = $this->riotapi_model->getSummonerByName($region, $summonerinput);
			$riotsummoners['region'] = trim($region);
			print_r($riotsummoners);
			//contains Array ( [summonername] => Array ( [id] => 39895516 [name] => Summoner Name [profileIconId] => 0 [summonerLevel] => 6 [revisionDate] => 1383423931000 ) [region] => Region )
			if(!array_key_exists($summonerinput, $riotsummoners))
			{
				$data['errormessage'] = "The specified summoner was not found in the specified region";
				$this->load->view('messages/rune_page_verification_fail', $data);
				return;
			}
			else
			{
				//check to see if summoner is banned
				$banned_summoner = $this->banned_model->get_bysummonername($riotsummoners[$summonerinput]['name']);
				//summoner exists, check if summoner exists already in our db
				$summoner = $this->lol_model->registered_summoner($riotsummoners[$summonerinput]['name']);
				if($banned_summoner) 
				{
					$data['errormessage'] = "The specified summoner has been banned from our website";
					$this->load->view('messages/rune_page_verification_fail', $data);
					return;
				}
				else if(!$summoner)
				{
					//summoner doesn't exist in db yet. Generate a Rune Page Key
					$_SESSION['runepagekey'] = $this->user_model->generate_rune_page_key();
					$data['runepagekey'] = $_SESSION['runepagekey'];
					$_SESSION['summoner'] = $riotsummoners[$summonerinput];
					$_SESSION['summoner']['region'] = $region;
					$summonerid = $_SESSION['summoner']['id'];
			  		$runepagekey = $_SESSION['runepagekey'];
			  		$runepages = $this->riotapi_model->getSummoner($summonerid,"runes");
					$this->load->view('ajax/authenticate_summoner',$data);
					return;
				}
				else
				{
					//summoner already existing return error
					$data['errormessage'] = "Summoner is already registered in our database";
					$this->load->view('messages/rune_page_verification_fail', $data);
					return;
				}
			}
		}
  	}//end function

  	public function rune_page_verification()
  	{
  		$summonerid = $_SESSION['summoner']['id'];
  		$runepagekey = $_SESSION['runepagekey'];
  		$runepages = $this->riotapi_model->getSummoner($summonerid,"runes");
  		
  		$firstRunePageName = $runepages[$summonerid]['pages']['0']['name'];
  		if($firstRunePageName == $runepagekey)
  		{
  			//user runepage is validated, re-check absence in db
  			$summoner = $this->lol_model->get_uid_from_summonerid($summonerid);
  			if(!$summoner)
  			{
  				//redirects to user/create_summoner
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

  	public function find_team_lol($teamname)
  	{
  		$esportid = 1; // LoL Esport id
  		$teamname = trim(urldecode($teamname));
  		$data['team_lol_result'] = $this->team_model->get_team_lol_byname($teamname);
  		if(!$data['team_lol_result'])
  		{
  			//user was registered during verification phase (highly unlikely), display error
      		$data['errormessage'] = "Team couldn't be found, make sure the spelling is correct (including caps).";
			$this->load->view('messages/rune_page_verification_fail', $data);
  		}
  		else
  		{
  			$team = $this->team_model->get_team_by_captainid($_SESSION['user']['UserId'],$esportid);
  			if($team['name'] == $teamname)
  			{
  				//user trying to trade with own team, deny him.
	      		$data['errormessage'] = "Nice try. You can't trade with your own team.";
				$this->load->view('messages/rune_page_verification_fail', $data);
  			}
  			else
  			{
  				$this->load->view('ajax/team_lol_search_result',$data);
  			}
  		}
  	}
}
