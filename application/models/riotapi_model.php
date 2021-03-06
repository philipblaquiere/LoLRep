<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Riotapi_model extends CI_Model  {

	const API_URL_1_1 = 'http://prod.api.pvp.net/api/lol/{region}/v1.1/';
	const API_URL_1_2 = 'http://prod.api.pvp.net/api/lol/{region}/v1.2/';
	const API_URL_1_3 = 'http://prod.api.pvp.net/api/lol/{region}/v1.3/';
	const API_URL_1_4 = 'http://prod.api.pvp.net/api/lol/{region}/v1.4/';
	const API_URL_2_1 = 'http://prod.api.pvp.net/api/lol/{region}/v2.2/';
	const API_URL_2_3 = 'http://prod.api.pvp.net/api/lol/{region}/v2.3/';
	const API_KEY = 'ca862537-7979-4f59-82d3-8afa7fe2d93a';
	const RATE_LIMIT_MINUTES = 500;
	const RATE_LIMIT_SECONDS = 10;
	const CACHE_LIFETIME_MINUTES = 60;
	const CACHE_ENABLED = true;
	private $REGION;
	
	public function __construct()
	{		
		$this->REGION = 'na';
	}

	public function getChampions(){
		$call = 'champion';
		$this->REGION = 'na';
		//add API URL to the call
		$call = self::API_URL_1_1 . $call;
		return $this->request($call);
	}

	public function getRecentGames($id){
		$call = 'game/by-summoner/' . $id . '/recent';

		//add API URL to the call
		$call = self::API_URL_1_3 . $call;

		return $this->request($call);
	}

	public function getLeague($id){
		$call = 'league/by-summoner/' . $id;
		$this->REGION = 'na';
		//add API URL to the call
		$call = self::API_URL_2_3 . $call;

		return $this->request($call);
	}

	public function getStats($id,$option='summary'){
		$call = 'stats/by-summoner/' . $id . '/' . $option;

		//add API URL to the call
		$call = self::API_URL_1_2 . $call;

		return $this->request($call);
	}

	public function getSummoner($id,$option=null){
		$call = 'summoner/' . $id;
		$this->REGION = 'na';
		switch ($option) {
			case 'masteries':
				$call .= '/masteries';
				break;
			case 'runes':
				$call .= '/runes';
				break;
			case 'name':
				$call .= '/name';
				break;
			
			default:
				//do nothing
				break;
		}

		//add API URL to the call
		$call = self::API_URL_1_4 . $call;

		return $this->request($call);
	}


	public function getSummonerByName($region, $name){
		$this->REGION = 'na';

		//sanitize name a bit - this will break weird characters
		$name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
		$call = 'summoner/by-name/' . $name;

		//add API URL to the call
		$call = self::API_URL_1_4 . $call;

		return $this->request($call);
	}


	public function getTeam($id){
		$call = 'team/by-summoner/' . $id;

		//add API URL to the call
		$call = self::API_URL_2_1 . $call;

		return $this->request($call);
	}

	private function request($call){

		//probably should put rate limiting stuff here


		//format the full URL
		$url = $this->format_url($call);
		$result=array();
		//call the API and return the result
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$values = json_decode($result,TRUE);

		return $values;	

	}

	//creates a full URL you can query on the API
	private function format_url($call) {
		return str_replace('{region}', $this->REGION, $call) . '?api_key=' . self::API_KEY;
	}
}