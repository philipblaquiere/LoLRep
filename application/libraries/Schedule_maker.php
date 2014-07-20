<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Schedule_maker {

	private $num_teams;
	private $start_date;
	private $end_date;
	private $duration;
	private $all_matches;
	private $initial_match_delay = 2;

	public function __construct()
	{
    }

    public function get_end_date()
    {
    	return $this->end_date;
    }
    public function set_num_teams($numteams) {
    	$this->num_teams = $numteams;
    }

    public function set_start_date($start) {
    	$this->start_date = $start;
    	
    }
    public function set_duration($duration)
    {
    	$startdate = strtotime($this->start_date);
    	$this->end_date = new DateTime("@$startdate");
    	$this->end_date->modify('+ ' . $duration . ' month');
    	$this->end_date = $this->end_date->getTimestamp();
    }

    public function set_matches($match_array) {
    	$season_matches = array();
    	$week_num = 0;
    	$end_of_season = FALSE;
    	$match_array = $this->_bring_matches_uptodate($match_array);

    	//Scheduled matches end a week early to allow time for tournament
    	$season_end_date = new DateTime("@$this->end_date");
    	$season_end_date->modify('-1 week');
    	$season_end_date_timestamp = $season_end_date->getTimestamp();
    	$week_num = 0;
    	while (!$end_of_season)
    	{
    		foreach ($match_array as $match_time) {
    			$next_match = new DateTime("@$match_time");
    			$next_match->modify('+'. $week_num .' week');
    			if($next_match->getTimestamp() < $season_end_date_timestamp) {
    				//Next Match is still under season time
    				array_push($season_matches, $next_match->getTimestamp());
    			}
    			else {
    				$end_of_season = TRUE;
    			}
			}
			$week_num += 1;
    	}
    	$this->all_matches = $season_matches;
    }

    public function generate_schedule() {
    	$schedule = array();
		$shuffled_teams = range(0, ($this->num_teams - 1));
		shuffle($shuffled_teams);

		if($this->num_teams % 2 == 0) {
			//Even Number of Teams
			foreach ($this->all_matches as $match)
			{
				$shuffled_teams = $this->_shift_teams_right($shuffled_teams);
				for ($i=0; $i < $this->num_teams/2; $i++) { 
					$match_details = array();
					$match_details['teamaid'] = $shuffled_teams[$i];
					$match_details['teambid'] = $shuffled_teams[($this->num_teams - 1 - $i)];
					$match_details['match_date'] = $match;
					array_push($schedule, $match_details);
				}
			}
			return $schedule;
    	}
		else {
			//Odd Number of Teams
			foreach ($this->all_matches as $match) {
				$shuffled_teams = $this->_shift_teams_right($shuffled_teams);
				for ($i=0; $i < $this->num_teams/2; $i++) { 
					array_push($schedule, array($shuffled_teams[$i], $shuffled_teams[$i+($this->num_teams/2)], $match));
				}
			}
			return $schedule;
		}
	}

	/*
	* This function converts first matchs (stored as
	* a unix timestamp the moment the league was created) to times
	* that are after the season start date.
	*/
	private function _bring_matches_uptodate($first_matches)
	{
		$match_number = 0;
		$updated_matches = array();
		$startdate = strtotime($this->start_date);
		$startdate = new DateTime("@$startdate");
		$startdate->modify('+' . $this->initial_match_delay . ' day');
		foreach ($first_matches as $first_match) 
		{
			$first_match = new DateTime("@$first_match");
			if($first_match->getTimestamp() < $startdate->getTimestamp())
			{
				
				while($first_match->getTimestamp() < $startdate->getTimestamp())
				{
					$first_match->modify('+1 week');
				}
				array_push($updated_matches, $first_match->getTimestamp());
				$match_number += 1;
			}
			else
			{
				array_push($updated_matches, $first_match->getTimestamp());
				$match_number += 1;
			}
		}
		asort($updated_matches);
		return $updated_matches;
	}

	private function _shift_teams_right($teams) 
	{
		$shifted_teams = array();
		//set pivot
		array_push($shifted_teams, $teams[0]);
		array_push($shifted_teams, $teams[(count($teams)-1)]);
		for ($i = 1; $i < $this->num_teams - 1; $i++)
		{ 
			array_push($shifted_teams, $teams[$i]);
		}
		return $shifted_teams;
	}
}

/* End of file Schedule_maker.php */