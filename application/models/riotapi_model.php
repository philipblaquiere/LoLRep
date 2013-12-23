<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Riotapi_model extends CI_Model  {

	const API_URL_1_1 = 'http://prod.api.pvp.net/api/lol/{region}/v1.1/';
	const API_URL_1_2 = 'http://prod.api.pvp.net/api/lol/{region}/v1.2/';
	const API_URL_2_1 = 'http://prod.api.pvp.net/api/lol/{region}/v2.2/';
	const API_KEY = 'API_KEY_HERE';
	const RATE_LIMIT_MINUTES = 500;
	const RATE_LIMIT_SECONDS = 10;
	const CACHE_LIFETIME_MINUTES = 60;
	const CACHE_ENABLED = true;
	private $REGION;
	
	public function __construct()
	{
		$this->REGION = "na";		
	}

	public function getChampion(){
		$call = 'champion';

		//add API URL to the call
		$call = self::API_URL_1_1 . $call;

		return $this->request($call);
	}

	public function getGame($id){
		$call = 'game/by-summoner/' . $id . '/recent';

		//add API URL to the call
		$call = self::API_URL_1_2 . $call;

		return $this->request($call);
	}

	public function getLeague($id){
		$call = 'league/by-summoner/' . $id;

		//add API URL to the call
		$call = self::API_URL_2_2 . $call;

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
		$call = self::API_URL_1_2 . $call;

		return $this->request($call);
	}


	public function getSummonerByName($name){


		//sanitize name a bit - this will break weird characters
		$name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
		$call = 'summoner/by-name/' . $name;

		//add API URL to the call
		$call = self::API_URL_1_2 . $call;

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

		//caching
		if(self::CACHE_ENABLED){
			$cacheFile = 'cache/' . md5($url);

		    if (file_exists($cacheFile)) {
		        $fh = fopen($cacheFile, 'r');
		        $cacheTime = trim(fgets($fh));

		        // if data was cached recently, return cached data
		        if ($cacheTime > strtotime('-'. CACHE_LIFETIME_MINUTES . ' minutes')) {
		            return fread($fh,filesize($cacheFile));
		        }

		        // else delete cache file
		        fclose($fh);
		        unlink($cacheFile);
		    }
		}

		//call the API and return the result
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		
		if(self::CACHE_ENABLED){
			//create cache file
		    $fh = fopen($cacheFile, 'w');
		    fwrite($fh, time() . "\n");
		    fwrite($fh, $result);
		    fclose($fh);
		}

		return $result;	

	}

	//creates a full URL you can query on the API
	private function format_url($call){
		return str_replace('{region}', $this->REGION, $call) . '?api_key=' . self::API_KEY;
	}
}




?>
