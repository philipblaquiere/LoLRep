<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Schedule_maker {

	private $num_teams;
	private $start_date;
	private $end_date;
	private $all_matches;

	public function __construct() {
    }

    public function set_num_teams($numteams) {
    	$this->num_teams = $numteams;
    }

    public function set_start_end_dates($start,$end) {
    	$this->start_date = $start;
    	$this->end_date = $end;
    }

    public function set_matches($match_array) {
    	$season_matches = array();
    	$week_num = 0;
    	$end_of_season = False;

    	//Scheduled matches end a week early to allow time for tournment
    	$season_end_date = new DateTime("@$this->end_date");
    	$season_end_date->modify('-1 week');
    	$season_end_date_timestamp = $season_end_date->getTimestamp();
    	$week_num = 0;
    	while (!$end_of_season) {
    		foreach ($match_array as $match_time) {
    			$next_match = new DateTime("@$match_time");
    			$next_match->modify('+'. $week_num .' week');
    			if($next_match->getTimestamp() < $season_end_date_timestamp) {
    				//Next Match is still under season time
    				array_push($season_matches, $next_match->getTimestamp());
    			}
    			else {
    				$end_of_season = True;
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
			foreach ($this->all_matches as $match) {
				$shuffled_teams = $this->shift_teams_right($shuffled_teams);
				for ($i=0; $i < $this->num_teams/2; $i++) { 
					$match_details = array();
					$match_details['teamaid'] = $shuffled_teams[$i];
					$match_details['teambid'] = $shuffled_teams[$i+($this->num_teams/2)];
					$match_details['match_date'] = $match;
					array_push($schedule, $match_details);
				}
			}
			return $schedule;
    	}
		else {
			//Odd Number of Teams
			foreach ($this->all_matches as $match) {
				$shuffled_teams = $this->shift_teams_right($shuffled_teams);
				for ($i=0; $i < $this->num_teams/2; $i++) { 
					array_push($schedule, array($shuffled_teams[$i], $shuffled_teams[$i+($this->num_teams/2)], $match));
				}
			}
			return $schedule;
		}
	}
	private function shift_teams_right($teams) {
		$last_team = $teams[count($teams) - 1];
		$shifted_teams = array();
		array_push($shifted_teams, $teams[count($teams) - 1]);
		for ($i = 0; $i < count($teams) - 1; $i++) { 
			array_push($shifted_teams, $teams[$i]);
		}
		return $shifted_teams;
	}
}

/* End of file Schedule_maker.php */