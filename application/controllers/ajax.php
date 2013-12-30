<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {

	public function __construct(){
	    parent::__construct();
	    $this->load->model('user_model');
	    $this->load->model('lol_model');
	    $this->load->model('riotapi_model');
	    $this->load->model('system_message_model');
	}

	public function authenticate_summoner($region, $summonerinput) {

	    if($summonerinput== "-") {
	      //user didn't enter anything, show eror message and reload.
		    $data['errormessage'] = "You must enter a summoner name to validate.";
			$this->load->view('messages/rune_page_verification_fail', $data);
			return;
	    }
	    else if($region == "Region%20") {
	    	$data['errormessage'] = "You must select a region";
			$this->load->view('messages/rune_page_verification_fail', $data);
			return;
	    }
	    else {
	    	$region = strtolower($region);
			//check riot servers to see if summoner actually exists.
			$riotsummoner = $this->riotapi_model->getSummonerByName($region, $summonerinput);
			//contains Array ( [id] => 29208894 [name] => seejimmyrun [profileIconId] => 576 [summonerLevel] => 30 [revisionDate] => 1387724620000 )
			if(!$riotsummoner['id']) {
				$data['errormessage'] = "The specified summoner was not found in the specified region";
				$this->load->view('messages/rune_page_verification_fail', $data);
				return;
			}
			else {
				//summoner exists, check if summoner exists already in our db
				$summoner = $this->lol_model->registered_summoner($summonerinput);

				if(!$summoner) {
					//summoner doesn't exist in db yet. Generate a Rune Page Key
					$_SESSION['runepagekey'] = $this->user_model->generate_rune_page_key();
					$data['runepagekey'] = $_SESSION['runepagekey'];
					$_SESSION['summoner'] = $riotsummoner;
					$summonerid = $_SESSION['summoner']['id'];
			  		$runepagekey = $_SESSION['runepagekey'];
			  		$runepages = $this->riotapi_model->getSummoner($summonerid,"runes");
					$this->load->view('ajax/authenticate_summoner',$data);
					return;
				}
				else {
					//summoner already existing return error
					$data['errormessage'] = "Summoner is already registered in our database";
					$this->load->view('messages/rune_page_verification_fail', $data);
					return;
				}
			}
		}
  	}//end function

  	public function rune_page_verification() {
  		$summonerid = $_SESSION['summoner']['id'];
  		$runepagekey = $_SESSION['runepagekey'];
  		$runepages = $this->riotapi_model->getSummoner($summonerid,"runes");

  		$firstRunePageName = $runepages['pages']['0']['name'];
  		if($firstRunePageName == $runepagekey) {
  			//user runepage is validated, re-check absence in db
  			$summoner = $this->lol_model->get_uid_from_summonerid($summonerid);
  			if(!$summoner) {
  				//redirects to user/create_summoner
  				echo "success";
  			}
  			else {
  				//user was registered during verification phase (highly unlikely), display error
	      		$data['errormessage'] = "The specified summoner is already registered";
				$this->load->view('messages/rune_page_verification_fail', $data);
  			}
  		}
  		else {
  			//user is invalid, display error message.
  			$data['errormessage'] = "Incorrect Rune page name (" . $firstRunePageName . "), should be " . $runepagekey;
  			$this->load->view('messages/rune_page_verification_fail', $data);
  		}
  	}
}
