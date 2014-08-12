<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Stats_formatter
{
	const NUM_DEATHS_LOL = "numDeaths";
	const ASSISTS_LOL = "assists";
	const CHAMPIONS_KILLED_LOL = "championsKilled";
	const TIME_PLAYED_LOL = "timePlayed";
	const GOLD_EARNED_LOL = "goldEarned";
	const MINIONS_KILLED_LOL = "minionsKilled";
	const AVERAGE_KDA_LOL = "kda_average";
	const AVERAGE_GPM_LOL = "gpm_average";
	const AVERAGE_GPG_LOL = "gpg_average";
	const AVERAGE_CS_LOL = "cs_average";
	const AVERAGE_CS_PM_LOL = "cspm_average";

	public function __construct()
	{
    }

    public function calculate_averages($match_stats, $esportid)
    {
    	switch ($esportid) {
    		case '1':
    			return $this->_calculate_averages_lol($match_stats, $esportid);
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function _calculate_averages_lol($match_stats, $esportid)
    {
    	$stat_averages[self::AVERAGE_KDA_LOL] = array();
    	$stat_averages[self::AVERAGE_GPM_LOL] = array();
    	$stat_averages[self::AVERAGE_GPG_LOL] = array();
    	$stat_averages[self::AVERAGE_CS_LOL] = array();
    	$stat_averages[self::AVERAGE_CS_PM_LOL] = array();

    	foreach ($match_stats as $stats)
    	{
    		if(isset($stats[self::NUM_DEATHS_LOL]))
    		{
    			$this->_add_to_averages($stat_averages, $stats, $esportid);
    		}
    		else
    		{
    			foreach ($stats as $stats_player)
    			{
    				if(isset($stats_player[self::NUM_DEATHS_LOL]))
		    		{
		    			$this->_add_to_averages($stat_averages, $stats_player, $esportid);
		    		}
    			}
    		}
    	}
    	$stat_averages[self::AVERAGE_KDA_LOL] = round($this->_calculate_average($stat_averages[self::AVERAGE_KDA_LOL]), 1);
    	$stat_averages[self::AVERAGE_GPM_LOL] = round($this->_calculate_average($stat_averages[self::AVERAGE_GPM_LOL])/1000, 1);
    	$stat_averages[self::AVERAGE_GPG_LOL] = round($this->_calculate_average($stat_averages[self::AVERAGE_GPG_LOL])/1000, 1);
    	$stat_averages[self::AVERAGE_CS_LOL] = intval($this->_calculate_average($stat_averages[self::AVERAGE_CS_LOL]));
    	$stat_averages[self::AVERAGE_CS_PM_LOL] = intval($this->_calculate_average($stat_averages[self::AVERAGE_CS_PM_LOL]));
    	return $stat_averages;
    }

    private function _calculate_average($stats)
    {
    	$sum = 0;
    	foreach ($stats as $stat)
    	{
    		$sum+=$stat;
    	}
    	return $sum/count($stats);
    }

    private function _add_to_averages(&$stat_averages, $stats, $esportid)
    {
    	switch ($esportid) {
    		case '1':
    			array_push($stat_averages[self::AVERAGE_KDA_LOL], $this->_calculate_kda($stats, $esportid));
    			array_push($stat_averages[self::AVERAGE_GPM_LOL], $this->_calculate_gpm($stats, $esportid));
    			array_push($stat_averages[self::AVERAGE_GPG_LOL], $this->_calculate_gpg($stats, $esportid));
    			array_push($stat_averages[self::AVERAGE_CS_LOL], $this->_calculate_cs($stats, $esportid));
    			array_push($stat_averages[self::AVERAGE_CS_PM_LOL], $this->_calculate_cspm($stats, $esportid));
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function _calculate_kda($stats, $esportid)
    {
    	switch ($esportid)
    	{
    		case '1':
    			if(($stats[self::ASSISTS_LOL] + $stats[self::CHAMPIONS_KILLED_LOL]) == 0)
				{
					$stats[self::ASSISTS_LOL] = 1;
				}
				//If player has 0 deaths, make it 1
				$stats[self::NUM_DEATHS_LOL] = $stats[self::NUM_DEATHS_LOL] == 0 ? 1 : $stats[self::NUM_DEATHS_LOL];

				return ($stats[self::CHAMPIONS_KILLED_LOL] + $stats[self::ASSISTS_LOL]) / $stats[self::NUM_DEATHS_LOL];
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function _calculate_gpg($stats, $esportid)
    {
    	switch ($esportid)
    	{
    		case '1':
				return $stats[self::GOLD_EARNED_LOL];
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function _calculate_cspm($stats, $esportid)
    {
    	switch ($esportid)
    	{
    		case '1':
    			$cs = $stats[self::MINIONS_KILLED_LOL];
    			$time_minutes = $stats[self::TIME_PLAYED_LOL] / 60;
				return intval($cs/$time_minutes);
    		
    		default:
    			# code...
    			break;
    	}
    }

    private function _calculate_cs($stats, $esportid)
    {
    	switch ($esportid)
    	{
    		case '1':
				return $stats[self::MINIONS_KILLED_LOL];
    		
    		default:
    			# code...
    			break;
    	}
    }


    private function _calculate_gpm($stats, $esportid)
    {
    	switch ($esportid)
    	{
    		case '1':
    			$gold = $stats[self::GOLD_EARNED_LOL];
    			$time_minutes = $stats[self::TIME_PLAYED_LOL] / 60;
				return intval($gold/$time_minutes);
    		
    		default:
    			# code...
    			break;
    	}
    }
}
   