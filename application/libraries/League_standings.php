<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class League_standings
{
	public function __construct()
	{
    }

    public function get_standings($league, $matches)
    {
    	$current_season = $league['seasons'][$league['current_season']];
    	$teams = $current_season['teams'];
    	$sortable_teams = array();

    	foreach ($teams as &$team)
    	{
    		//key value of teamid and #wins
    		$sortable_teams[$team['teamid']] = 0;

    		$team['wins'] = 0;
    		$team['loss'] = 0;
    	}


    	foreach ($matches as $match)
    	{
    		if(isset($match['winnerid']) && $match['status'] == 'finished')
    		{
    			if($match['winnerid'] == $match['teamaid'])
	    		{
	    			$teams[$match['teamaid']]['wins'] += 1; 
	    			$sortable_teams[$match['teamaid']] +=1;
	    			$sortable_teams[$match['teambid']] -=1;
	    			$teams[$match['teambid']]['loss'] += 1;
	    		}
				else
				{
					$teams[$match['teamaid']]['loss'] += 1;
					$sortable_teams[$match['teamaid']] -=1;
					$sortable_teams[$match['teambid']] +=1;
					$teams[$match['teambid']]['wins'] += 1;
				}
    		}
    	}
    	arsort($sortable_teams);

    	$sorted_teams = array();
    	$rank = 0;
    	$last_score = 99909;
    	foreach ($sortable_teams as $sorted_teamid => $score)
    	{
    		if($last_score != $score)
    		{
    			$rank +=1;
    		}
    		$teams[$sorted_teamid]['rank'] = $rank;
    		array_push($sorted_teams, $teams[$sorted_teamid]);
    		$last_score = $score;
    	}
    	return $sorted_teams;
    }
}
    