<?php 
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
		$url = URL.URLPOSTFIX.$region.SLASH.VERSIONONE.CHAMPION;
		$result = getarray($url);
		return $result;
	}

	public function getRecentGamesById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONE.GAME.BYSUMMONER.$summonerId.SLASH.RECENT;
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
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONE.STATS.BYSUMMONER.$summonerId.SLASH.SUMMARY;
		$result = getarray($url);
		return $result;
	}

	public function getRankedStatsById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONE.STATS.BYSUMMONER.$summonerId.SLASH.RANKED;
		$result = getarray($url);
		return $result;
	}

	public function getMasteriesById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONE.SUMMONER.$summonerId.SLASH.MASTERIES;
		$result = getarray($url);
		return $result;
	}

	public function getRunesById($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONONE.SUMMONER.$summonerId.SLASH.RUNES;
		$result = getarray($url);
		return $result;
	}

	public function getSummonersListByIds($region,$summonerIds[])
	{

	}

	public function getTeamsFromId($region,$summonerId)
	{
		$url=URL.URLPOSTFIX.$region.SLASH.VERSIONTWO.TEAM.BYSUMMONER.$summonerId;
		$result = getarray($url);
		return $result;
	}
?>	