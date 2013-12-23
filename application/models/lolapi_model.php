<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define("URL","http://prod.api.pvp.net/api/");
define("URLPOSTFIX","lol/");
define("APIKEY","ee9af537-a4f1-4a7a-9e7c-7ca19e4aa7a3");
define("KEYPREFIX","?api_key=");
define("LEAGUE","league/");
define("CHAMPION","champion/");
define("BYSUMMONER","by-summoner/");
define("SUMMONER","summoner/");
define("BYNAME","by-name");
define("NAME","name");
define("STATS","stats/");
define("SUMMARY","summary");
define("RANKED","ranked");
define("RUNES","runes");
define("MASTERIES","masteries");
define("TEAM","team/");
define("RECENT","recent");
define("GAME","game");
define("SLASH","/");
define("VERSIONONETWO","v1.2/");
define("VERSIONONEONE","v1.1/");

class Lolapi_model extends CI_Model {

	public function __construct() {
    	parent::__construct();
    	//$this->load->library('curl');
 	}

	public function getarray($url)
	{
 		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$values = json_decode($output,TRUE);
		return $values;
	}

	public function getAllChampions($region)
	{
		$url = URL.URLPOSTFIX.$region.SLASH.VERSIONONEONE.CHAMPION;
		$result = getarray($url);
		return $result;
	}

	public function getRecentGamesById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONEONE.GAME.BYSUMMONER.$summonerId.SLASH.RECENT;
		$result = getarray($url);
		return $result;
	}

	public function getLeaguesDataById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONTWO.LEAGUE.BYSUMMONER.$summonerId;
		$result = getarray($url);
		return $result;
	}

	public function getStatSummaryById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONEONE.STATS.BYSUMMONER.$summonerId.SLASH.SUMMARY;
		$result = getarray($url);
		return $result;
	}

	public function getRankedStatsById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONEONE.STATS.BYSUMMONER.$summonerId.SLASH.RANKED;
		$result = getarray($url);
		return $result;
	}

	public function getMasteriesById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONEONE.SUMMONER.$summonerId.SLASH.MASTERIES;
		$result = getarray($url);
		return $result;
	}

	public function getRunesById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONEONE.SUMMONER.$summonerId.SLASH.RUNES;
		$result = getarray($url);
		return $result;
	}

	/*public function getSummonersListByIds($region,$summonerIds[])
	{

	}*/

	public function getTeamsFromId($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONETWO.TEAM.BYSUMMONER.$summonerId;
		$result = getarray($url);
		return $result;
	}

	public function getSummonerByName($region, $summonerName) {
		$url = URL.URLPOSTFIX.$region.SLASH.VERSIONONETWO.BYNAME.$summonerName.KEYPREFIX.APIKEY;
		$result = $this->getarray($url);
		return $result;
	}
}
